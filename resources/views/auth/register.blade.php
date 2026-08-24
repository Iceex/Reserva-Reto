@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-md pt-12">
    <div class="rounded-2xl border border-slate-800 bg-slate-900 p-8 shadow-2xl">
        <h1 class="text-2xl font-semibold">Crear cuenta</h1>
        <p class="mt-2 text-sm text-slate-400">Registro simple con sesión de Laravel.</p>

        <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm text-slate-300">Nombre</label>
                <input name="name" type="text" value="{{ old('name') }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Email</label>
                <input name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Contraseña</label>
                <input name="password" type="password" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none focus:border-indigo-500">
            </div>
            <div>
                <label class="mb-2 block text-sm text-slate-300">Confirmar contraseña</label>
                <input name="password_confirmation" type="password" required class="w-full rounded-xl border border-slate-700 bg-slate-950 px-4 py-3 outline-none focus:border-indigo-500">
            </div>
            <button class="w-full rounded-xl bg-indigo-600 px-4 py-3 font-medium hover:bg-indigo-500">Crear cuenta</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-400">¿Ya tenés cuenta? <a class="text-indigo-400 hover:text-indigo-300" href="{{ route('login') }}">Ingresá</a></p>
    </div>
</div>
@endsection
