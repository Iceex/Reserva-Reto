@extends('layouts.app')

@section('content')
<div class="grid gap-6 lg:grid-cols-[360px_1fr] items-start">

    {{-- nueva reserva --}}
    <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">
        <h1 class="text-xl font-semibold">
            Nueva reserva
        </h1>

        <p class="mt-2 text-sm leading-6 text-slate-400">
            La ubicación se asigna automáticamente en orden A → B → C → D.
            Se pueden utilizar hasta 3 mesas de una misma ubicación.
        </p>

        <p class="mt-2 text-sm leading-6 text-red-400">
            Capacidad de 2 personas por mesa
        </p>

        <form
            method="POST"
            action="{{ route('reservas.store') }}"
            class="mt-6 space-y-5"
        >
            @csrf

            {{-- fecha --}}
            <div>
                <label
                    for="fecha"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Fecha
                </label>

                <div class="relative">
                    <input
                        id="fecha"
                        name="fecha"
                        type="date"
                        value="{{ old('fecha', $fecha) }}"
                        min="{{ now()->format('Y-m-d') }}"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 pr-12 text-white outline-none transition focus:border-indigo-500"
                    >

                    <label
                        for="fecha"
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                            />
                        </svg>
                    </label>
                </div>

                @error('fecha')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- hora --}}
            <div>
                <label
                    for="hora"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Hora
                </label>

                <div class="relative">
                    <input
                        id="hora"
                        name="hora"
                        type="time"
                        value="{{ old('hora', '12:00') }}"
                        required
                        class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 pr-12 text-white outline-none transition focus:border-indigo-500"
                    >

                    <label
                        for="hora"
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-slate-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-5 w-5"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                            />

                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 7v5l3 2"
                            />
                        </svg>
                    </label>
                </div>

                <p
                    id="horario-ayuda"
                    class="mt-2 text-xs text-slate-500"
                >
                    Seleccioná una fecha para ver el horario disponible.
                </p>

                @error('hora')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- cantidad de personas --}}
            <div>
                <label
                    for="cantidad_personas"
                    class="mb-2 block text-sm text-slate-300"
                >
                    Cantidad de personas
                </label>

                <input
                    id="cantidad_personas"
                    name="cantidad_personas"
                    type="number"
                    min="1"
                    max="100"
                    value="{{ old('cantidad_personas', 2) }}"
                    required
                    class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 text-white outline-none transition focus:border-indigo-500"
                >

                @error('cantidad_personas')
                    <p class="mt-2 text-sm text-red-400">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- submit --}}
            <button
                type="submit"
                class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-medium transition hover:bg-indigo-500"
            >
                Buscar disponibilidad y reservar
            </button>
        </form>
    </section>


    {{-- listado --}}
    <section class="rounded-2xl border border-slate-800 bg-slate-900 p-6">

        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">

            <div>
                <h2 class="text-xl font-semibold">
                    Reservas por fecha
                </h2>

                <p class="mt-1 text-sm text-slate-400">
                    Consulta consolidada de reservas, ubicación y mesas.
                </p>
            </div>

            {{-- filtro por fecha --}}
            <form
                method="GET"
                action="{{ route('reservas.index') }}"
                class="flex gap-2"
            >
                <div class="relative">
                    <input
                        id="filtro_fecha"
                        name="fecha"
                        type="date"
                        value="{{ $fecha }}"
                        min="{{ now()->format('Y-m-d') }}"
                        class="rounded-xl border border-slate-700 bg-slate-950 px-3 py-2 pr-10 text-sm text-white outline-none transition focus:border-indigo-500"
                    >

                    <label
                        for="filtro_fecha"
                        class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3 text-slate-400"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="1.8"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M8 2v4m8-4v4M3 10h18M5 4h14a2 2 0 0 1 2 2v13a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2Z"
                            />
                        </svg>
                    </label>
                </div>

                <button
                    type="submit"
                    class="rounded-xl border border-slate-700 px-4 py-2 text-sm transition hover:bg-slate-800"
                >
                    Consultar
                </button>
            </form>

        </div>


        {{-- tabla --}}
        <div class="mt-6 overflow-x-auto rounded-xl border border-slate-800">

            <table class="min-w-full text-left text-sm">

                <thead class="bg-slate-950 text-slate-400">
                    <tr>
                        <th class="px-4 py-3">
                            Hora
                        </th>

                        <th class="px-4 py-3">
                            Usuario
                        </th>

                        <th class="px-4 py-3">
                            Personas
                        </th>

                        <th class="px-4 py-3">
                            Ubicación
                        </th>

                        <th class="px-4 py-3">
                            Mesas
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-800">

                    @forelse($reservas as $reserva)

                        <tr class="transition hover:bg-slate-800/40">

                            <td class="whitespace-nowrap px-4 py-4">
                                {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('H:i') }}
                                –
                                {{ \Carbon\Carbon::parse($reserva->fecha_fin)->format('H:i') }}
                            </td>

                            <td class="px-4 py-4">
                                {{ $reserva->usuario }}
                            </td>

                            <td class="px-4 py-4">
                                {{ $reserva->cantidad_personas }}
                            </td>

                            <td class="px-4 py-4">

                                <span class="rounded-lg bg-indigo-950 px-2.5 py-1 font-medium text-indigo-300">
                                    {{ $reserva->ubicacion }}
                                </span>

                            </td>

                            <td class="px-4 py-4">
                                {{ $reserva->mesas ?? '—' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="5"
                                class="px-4 py-10 text-center text-slate-500"
                            >
                                No hay reservas para esta fecha.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </section>

</div>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const fecha = document.getElementById('fecha');
    const hora = document.getElementById('hora');
    const ayuda = document.getElementById('horario-ayuda');
    const filtro = document.getElementById('filtro_fecha');

    [fecha, hora, filtro].forEach(input => {
        input.addEventListener('click', () => {
            if (typeof input.showPicker === 'function') {
                input.showPicker();
            }
        });
    });

    const horarios = {
        weekday: {
            min: '10:00',
            max: '23:59',
            texto: 'Lunes a viernes: 10:00–24:00',
        },

        saturday: {
            min: '22:00',
            max: '23:59',
            texto: 'Sábado: 22:00–02:00',
        },

        sunday: {
            min: '12:00',
            max: '16:00',
            texto: 'Domingo: 12:00–16:00',
        },
    };

    function actualizarHorario() {
        if (!fecha.value) {
            hora.removeAttribute('min');
            hora.removeAttribute('max');

            ayuda.textContent =
                'Seleccioná una fecha para ver el horario disponible.';

            return;
        }

        const date = new Date(`${fecha.value}T00:00:00`);
        const day = date.getDay();

        let config;

        if (day === 0) {
            config = horarios.sunday;
        } else if (day === 6) {
            config = horarios.saturday;
        } else {
            config = horarios.weekday;
        }

        hora.min = config.min;
        hora.max = config.max;

        ayuda.textContent = config.texto;

        if (
            hora.value &&
            (
                hora.value < config.min ||
                hora.value > config.max
            )
        ) {
            hora.value = config.min;
        }
    }

    fecha.addEventListener('change', actualizarHorario);

    actualizarHorario();
});
</script>
@endpush