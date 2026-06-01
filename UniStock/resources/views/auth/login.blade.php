@extends('layouts.app')

@section('content')
<x-auth-card title="INICIAR SESIÓN">
    <form method="POST" action="{{ route('login') }}" id="loginForm" novalidate>
        @csrf
        <div class="mb-3">
            <label class="bw-label" for="email">Correo Electrónico</label>
            <input id="email" type="email" name="email" class="form-control bw-input @error('email') is-invalid @enderror" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label class="bw-label" for="password">Contraseña</label>
            <input id="password" type="password" name="password" class="form-control bw-input @error('password') is-invalid @enderror" required autocomplete="current-password">
            @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label bw-label" for="remember">Mantener sesión</label>
            </div>
            @if (Route::has('password.request'))
                <a class="small text-muted" href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
            @endif
        </div>
        <button type="submit" id="loginSubmitBtn" class="btn w-100 bw-btn position-relative">
            <span class="btn-text">Entrar</span>
            <span class="btn-spinner d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Iniciando sesión...
            </span>
        </button>
    </form>
</x-auth-card>

<style>
    .btn-spinner .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.18em;
        vertical-align: middle;
    }
    .bw-btn:disabled {
        opacity: 0.75;
        cursor: not-allowed;
        transform: none !important;
    }
</style>

<script>
    (function() {
        const form = document.getElementById('loginForm');
        const btn = document.getElementById('loginSubmitBtn');
        if (!form || !btn) return;

        form.addEventListener('submit', function(e) {
            if (form.dataset.submitting === 'true') {
                e.preventDefault();
                return;
            }
            form.dataset.submitting = 'true';
            btn.disabled = true;
            btn.querySelector('.btn-text').classList.add('d-none');
            btn.querySelector('.btn-spinner').classList.remove('d-none');
        });
    })();
</script>
@endsection
