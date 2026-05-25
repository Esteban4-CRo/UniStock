@extends('layouts.app')

@section('content')
<style>
/* B&W Minimalist Login */
body {
    background-color: #ffffff;
    color: #000000;
    font-family: 'Inter', sans-serif;
}
.login-card {
    background: rgba(255,255,255,0.05);
    border: 1px solid #333;
    border-radius: 12px;
    padding: 2rem;
    max-width: 400px;
    margin: 0 auto;
    box-shadow: 0 8px 24px rgba(0,0,0,0.6);
    color: #fff;
}
body {
    background-color: #050505;
    color: #fff;
    font-family: 'Inter', sans-serif;
}
.form-control {
    background: transparent;
    border: 1px solid #555;
    color: #fff;
}
.form-control:focus {
    border-color: #fff;
    box-shadow: none;
    background: rgba(255,255,255,0.08);
}
.btn-primary {
    background: #fff;
    color: #000;
    border: none;
    width: 100%;
    padding: 0.75rem;
    font-weight: 600;
}
.btn-primary:hover {
    background: #f0f0f0;
}
.divider span { color: #aaa; }
    background: #f9f9f9;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 2rem;
    max-width: 420px;
    margin: 0 auto;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}
.login-card h2 {
    font-weight: 600;
    margin-bottom: 1.5rem;
    text-align: center;
    color: #111;
}
.form-control {
    background: #fff;
    border: 1px solid #ccc;
    color: #111;
}
.form-control:focus {
    border-color: #000;
    box-shadow: none;
}
.btn-primary {
    background: #000;
    border: none;
    color: #fff;
    width: 100%;
    padding: 0.75rem;
    font-weight: 600;
}
.btn-primary:hover {
    background: #111;
}
.divider {
    display: flex;
    align-items: center;
    margin: 1.5rem 0;
}
.divider::before,
.divider::after {
    content: '';
    flex: 1;
    height: 1px;
    background: #ccc;
}
.divider span {
    padding: 0 0.5rem;
    color: #666;
    font-size: 0.9rem;
}
</style>

<div class="login-card">
    <h2>Iniciar Sesión</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Correo Electrónico</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Contraseña</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                <label class="form-check-label" for="remember">Mantener sesión</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="small text-muted">¿Olvidaste tu contraseña?</a>
            @endif
        </div>
        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>
    <div class="divider"><span>o</span></div>
    <a href="{{ route('google.login') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-center">
        <svg class="me-2" width="20" height="20" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
        Google
    </a>
</div>
@endsection
