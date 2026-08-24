<?php

namespace App\Http\Controllers;

use App\Services\ReservaService;
use Carbon\CarbonImmutable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ReservaController extends Controller
{
    public function __construct(private readonly ReservaService $service) {}

    public function index(Request $request): View
    {
        $fecha = $request->query('fecha', now()->toDateString());

        $reservas = DB::table('reservas as r')
            ->join('users as u', 'u.id', '=', 'r.user_id')
            ->leftJoin('reserva_mesa as rm', 'rm.reserva_id', '=', 'r.id')
            ->leftJoin('mesas as m', 'm.id', '=', 'rm.mesa_id')
            ->whereDate('r.fecha_inicio', $fecha)
            ->selectRaw('r.id, r.fecha_inicio, r.fecha_fin, r.cantidad_personas, r.ubicacion, u.name as usuario, GROUP_CONCAT(m.numero, \', \' ) as mesas')
            ->groupBy('r.id', 'r.fecha_inicio', 'r.fecha_fin', 'r.cantidad_personas', 'r.ubicacion', 'u.name')
            ->orderBy('r.fecha_inicio')
            ->orderBy('r.ubicacion')
            ->get();

        return view('reservas.index', compact('reservas', 'fecha'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fecha' => ['required', 'date_format:Y-m-d'],
            'hora' => ['required', 'date_format:H:i'],
            'cantidad_personas' => ['required', 'integer', 'min:1', 'max:100'],
        ]);

        try {
            $inicio = CarbonImmutable::createFromFormat(
                'Y-m-d H:i',
                $data['fecha'].' '.$data['hora'],
                config('app.timezone')
            );

            $reserva = $this->service->crear(
                auth()->id(),
                $inicio,
                (int) $data['cantidad_personas']
            );

            return back()->with('success', sprintf(
                'Reserva #%d creada en ubicación %s. Mesas: %s.',
                $reserva->id,
                $reserva->ubicacion,
                $reserva->mesas->pluck('numero')->implode(', ')
            ));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            report($e);
            return back()->withErrors(['fecha' => 'No se pudo crear la reserva. Intente nuevamente.'])->withInput();
        }
    }
}
