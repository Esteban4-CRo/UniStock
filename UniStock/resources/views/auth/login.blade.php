@extends('layouts.app')

@section('title', 'Iniciar Sesión — UniStock')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-sm-10 col-md-7 col-lg-5">

        <div class="text-center mb-4">
            <div style="width:58px;height:58px;background:#1abc9c;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:.75rem;">
                <i class="fas fa-cube text-white" style="font-size:1.6rem;"></i>
            </div>
            <h1 style="font-size:1.6rem;font-weight:800;">Iniciar Sesión</h1>
            <p class="text-muted" style="font-size:.9rem;">Ingresa a tu cuenta de UniStock</p>
        </div>

        <div class="card">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label" for="email">
                            <i class="fas fa-envelope me-1 text-muted"></i> Correo Electrónico
                        </label>
                        <input id="email" type="email"
                               class="form-control @error('email') is-invalid @enderror"
                               name="email" value="{{ old('email') }}"
                               required autocomplete="email" autofocus
                               placeholder="correo@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="password">
                            <i class="fas fa-lock me-1 text-muted"></i> Contraseña
                        </label>
                        <div class="input-group">
                            <input id="password" type="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   name="password" required
                                   placeholder="••••••••">
                            <button class="btn btn-secondary" type="button"
                                    onclick="const p=document.getElementById('password'); p.type=p.type==='password'?'text':'password';">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <div class="text-danger mt-1" style="font-size:.83rem;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember" style="font-size:.88rem;">Recordarme</label>
                        </div>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" style="font-size:.88rem;">¿Olvidaste tu contraseña?</a>
                        @endif
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2" style="font-size:1rem;">
                        <i class="fas fa-sign-in-alt me-1"></i> Ingresar
                    </button>
                </form>
            </div>
        </div>

        <p class="text-center mt-3" style="font-size:.9rem; color:#888;">
            ¿No tienes cuenta?
            <a href="{{ route('register') }}" style="font-weight:700;">Regístrate aquí</a>
        </p>
    </div>
</div>
@endsection
