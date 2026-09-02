<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_usuario_puede_crear_una_reserva(): void
    {
        $this->seed();
        $user = User::first();
        $fecha = now()->addDay()->next('Monday')->toDateString();

        $response = $this->actingAs($user)->post(route('reservas.store'), [
            'fecha' => $fecha,
            'hora' => '12:00',
            'cantidad_personas' => 4,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseCount('reservas', 1);
        $this->assertDatabaseCount('reserva_mesa', 1);
    }

    public function test_no_permite_reservar_con_menos_de_15_minutos(): void
    {
        $this->seed();
        $user = User::first();
        $inicio = now()->addMinutes(5);

        $response = $this->actingAs($user)->post(route('reservas.store'), [
            'fecha' => $inicio->toDateString(),
            'hora' => $inicio->format('H:i'),
            'cantidad_personas' => 2,
        ]);

        $response->assertSessionHasErrors('fecha');
        $this->assertDatabaseCount('reservas', 0);
    }

    public function test_no_permite_mas_de_tres_mesas_por_reserva(): void
    {
        $this->seed();
        $user = User::first();

        // La capacidad máxima de tres mesas es 8 personas con este seed.
        $fecha = now()->addWeek()->next('Monday')->toDateString();

        $response = $this->actingAs($user)->post(route('reservas.store'), [
            'fecha' => $fecha,
            'hora' => '12:00',
            'cantidad_personas' => 9,
        ]);

        $response->assertSessionHasErrors('fecha');
    }
}
