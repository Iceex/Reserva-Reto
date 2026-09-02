<?php

namespace App\Services;

use App\Models\Mesa;
use App\Models\Reserva;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReservaService
{
    private const DURACION_MINUTOS = 120;
    private const MAX_MESAS = 3;
    private const UBICACIONES = ['A', 'B', 'C', 'D'];

    public function crear(int $userId, CarbonImmutable $inicio, int $personas): Reserva
    {
        $fin = $inicio->addMinutes(self::DURACION_MINUTOS);
        $this->validarHorario($inicio);
        $this->validarAnticipacion($inicio);

        return DB::transaction(function () use ($userId, $inicio, $fin, $personas) {
            foreach (self::UBICACIONES as $ubicacion) {
                $mesas = Mesa::query()
                    ->where('ubicacion', $ubicacion)
                    ->orderBy('numero')
                    ->lockForUpdate()
                    ->get();

                if ($mesas->isEmpty()) {
                    continue;
                }

                // El cache acelera la lectura, pero no se considera fuente de verdad.
                $disponibles = $this->mesasDisponibles($ubicacion, $inicio, $fin);
                $disponibles = $mesas->whereIn('id', $disponibles->pluck('id'));

                // Volvemos a verificar dentro de la transacción después del lock.
                $ocupadas = DB::table('reserva_mesa as rm')
                    ->join('reservas as r', 'r.id', '=', 'rm.reserva_id')
                    ->whereIn('rm.mesa_id', $mesas->pluck('id'))
                    ->where('r.fecha_inicio', '<', $fin)
                    ->where('r.fecha_fin', '>', $inicio)
                    ->pluck('rm.mesa_id');

                $disponibles = $disponibles->reject(fn (Mesa $mesa) => $ocupadas->contains($mesa->id))->values();
                $seleccion = $this->seleccionarMesas($disponibles, $personas);

                if ($seleccion === null) {
                    continue;
                }

                $reserva = Reserva::create([
                    'user_id' => $userId,
                    'fecha_inicio' => $inicio,
                    'fecha_fin' => $fin,
                    'cantidad_personas' => $personas,
                    'ubicacion' => $ubicacion,
                ]);

                $reserva->mesas()->attach($seleccion->pluck('id'));
                $this->olvidarCache($ubicacion, $inicio, $fin);

                return $reserva->load('mesas');
            }

            throw ValidationException::withMessages([
                'fecha' => 'No hay disponibilidad para esa fecha, hora y cantidad de personas.',
            ]);
        }, attempts: 3);
    }

    public function disponibilidad(string $ubicacion, CarbonImmutable $inicio): array
    {
        $fin = $inicio->addMinutes(self::DURACION_MINUTOS);
        return $this->mesasDisponibles($ubicacion, $inicio, $fin)->pluck('id')->all();
    }

    private function mesasDisponibles(string $ubicacion, CarbonImmutable $inicio, CarbonImmutable $fin)
    {
        $key = $this->cacheKey($ubicacion, $inicio, $fin);

        return Cache::remember($key, now()->addSeconds(30), function () use ($ubicacion, $inicio, $fin) {
            $ocupadas = DB::table('reserva_mesa as rm')
                ->join('reservas as r', 'r.id', '=', 'rm.reserva_id')
                ->where('r.ubicacion', $ubicacion)
                ->where('r.fecha_inicio', '<', $fin)
                ->where('r.fecha_fin', '>', $inicio)
                ->pluck('rm.mesa_id');

            return Mesa::query()
                ->where('ubicacion', $ubicacion)
                ->whereNotIn('id', $ocupadas)
                ->orderBy('numero')
                ->get();
        });
    }

    private function seleccionarMesas($mesas, int $personas): ?\Illuminate\Support\Collection
    {
        $mejor = null;

        $this->combinaciones($mesas->values()->all(), self::MAX_MESAS, function (array $comb) use ($personas, &$mejor) {
            $capacidad = array_sum(array_map(fn (Mesa $mesa) => $mesa->capacidad, $comb));
            if ($capacidad < $personas) {
                return;
            }

            $candidato = collect($comb);
            $score = [count($comb), $capacidad - $personas, $candidato->sum('numero')];

            if ($mejor === null || $score < $mejor['score']) {
                $mejor = ['score' => $score, 'mesas' => $candidato];
            }
        });

        return $mejor['mesas'] ?? null;
    }

    private function combinaciones(array $items, int $max, callable $callback, array $actual = [], int $offset = 0): void
    {
        if ($actual !== []) {
            $callback($actual);
        }

        if (count($actual) >= $max) {
            return;
        }

        for ($i = $offset, $count = count($items); $i < $count; $i++) {
            $next = $actual;
            $next[] = $items[$i];
            $this->combinaciones($items, $max, $callback, $next, $i + 1);
        }
    }

    private function validarHorario(CarbonImmutable $inicio): void
    {
        $dia = $inicio->dayOfWeekIso;
        $minutos = ((int) $inicio->format('H')) * 60 + (int) $inicio->format('i');

        $valido = match ($dia) {
            1, 2, 3, 4, 5 => $minutos >= 600 && $minutos < 1440,
            6 => $minutos >= 1320,
            7 => $minutos >= 720 && $minutos < 960,
            default => false,
        };

        if (!$valido) {
            throw ValidationException::withMessages([
                'hora' => 'El horario solicitado está fuera del horario permitido.',
            ]);
        }
    }

    private function validarAnticipacion(CarbonImmutable $inicio): void
    {
        if ($inicio->lt(now()->addMinutes(15))) {
            throw ValidationException::withMessages([
                'fecha' => 'La reserva debe realizarse con al menos 15 minutos de anticipación.',
            ]);
        }
    }

    private function cacheKey(string $ubicacion, CarbonImmutable $inicio, CarbonImmutable $fin): string
    {
        return sprintf('disponibilidad:%s:%s:%s', $ubicacion, $inicio->format('YmdHi'), $fin->format('YmdHi'));
    }

    private function olvidarCache(string $ubicacion, CarbonImmutable $inicio, CarbonImmutable $fin): void
    {
        Cache::forget($this->cacheKey($ubicacion, $inicio, $fin));
    }
}
