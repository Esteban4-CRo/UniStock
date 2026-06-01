@props(['title' => ''])
@php
    /**
     * Blade component for authentication cards (login & register)
     *
     * Props:
     *   - title: string, heading displayed above the form
     */
@endphp
<style>
    /* Diseño Profesional - Azul, Negro y Blanco */
    body {
        background-color: #f0f4f8;
        color: #1a1a2e;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    }

    .auth-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 20px 60px -15px rgba(0, 50, 100, 0.12);
        overflow: hidden;
    }

    .bw-input {
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
        border-radius: 10px !important;
        color: #1a1a2e !important;
        padding: 0.75rem 1rem !important;
        transition: all 0.3s ease !important;
        box-shadow: none !important;
    }

    .bw-input:focus {
        border-color: #2563eb !important;
        background: #ffffff !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12) !important;
    }

    .bw-input::placeholder {
        color: #94a3b8;
    }

    .bw-label {
        color: #475569;
        font-size: .8rem;
        text-transform: uppercase;
        letter-spacing: .1em;
        font-weight: 600;
        margin-bottom: 0.4rem;
    }

    .bw-btn {
        background: #1a1a2e;
        color: #ffffff;
        font-weight: 700;
        border-radius: 12px;
        padding: 0.85rem;
        border: none;
        transition: all .3s ease;
        text-transform: uppercase;
        letter-spacing: .1em;
    }

    .bw-btn:hover {
        background: #2563eb;
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px -5px rgba(37, 99, 235, 0.4);
    }

    .bw-btn-google {
        background: #ffffff;
        color: #1a1a2e;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: .75rem;
        font-weight: 600;
        transition: all .3s;
    }

    .bw-btn-google:hover {
        background: #f0f4f8;
        border-color: #2563eb;
        color: #2563eb;
    }

    .separator {
        display: flex;
        align-items: center;
        text-align: center;
        color: #94a3b8;
        margin: 1.5rem 0;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .separator::before,
    .separator::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e2e8f0;
    }

    .separator:not(:empty)::before {
        margin-right: 1em;
    }

    .separator:not(:empty)::after {
        margin-left: 1em;
    }

    .logo-container {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .logo-container img {
        max-height: 80px;
        filter: none;
    }

    .auth-link {
        color: #2563eb;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        transition: color 0.2s;
    }

    .auth-link:hover {
        color: #1d4ed8;
        text-decoration: underline;
    }

    .auth-home-link {
        color: #64748b;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: color 0.2s;
    }

    .auth-home-link:hover {
        color: #1a1a2e;
    }

    .role-option label {
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        border-radius: 15px;
        cursor: pointer;
        display: block;
        text-align: center;
        color: #64748b;
        transition: all 0.3s;
        background: #f8fafc;
    }

    .role-option input[type="radio"]:checked + label {
        border-color: #2563eb;
        color: #2563eb;
        background: rgba(37, 99, 235, 0.06);
        transform: scale(1.02);
        box-shadow: 0 4px 15px -3px rgba(37, 99, 235, 0.2);
    }

    .role-icon {
        font-size: 2rem;
        margin-bottom: 0.75rem;
        display: block;
    }

    .form-check-input:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    .bw-btn-google:disabled,
    .bw-btn-google.loading {
        opacity: 0.75;
        cursor: wait;
        pointer-events: none;
        transform: none !important;
    }

    .bw-btn-google .spinner-border-sm {
        width: 1rem;
        height: 1rem;
        border-width: 0.18em;
    }
</style>
<script>
    (function() {
        const btn = document.getElementById('googleLoginBtn');
        if (!btn) return;
        btn.addEventListener('click', function(e) {
            if (btn.classList.contains('loading')) {
                e.preventDefault();
                return;
            }
            btn.classList.add('loading');
            const content = btn.querySelector('.google-btn-content');
            const spinner = btn.querySelector('.google-btn-spinner');
            if (content) content.classList.add('d-none');
            if (spinner) spinner.classList.remove('d-none');
        });
    })();
</script>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-5">
            <div class="auth-card">
                <div class="card-body p-4 p-md-5">
                    <div class="logo-container">
                        <img src="{{ asset('images/logo.png') }}" alt="UniStock Logo"
                            onerror="this.onerror=null; this.src=''; this.alt='[ LOGO ]'; this.style.color='#1a1a2e';">
                    </div>
                    <div class="text-center mb-4">
                        <h2 style="font-weight:800; letter-spacing:-1px; color:#1a1a2e;">{{ $title }}</h2>
                        <p style="color:#64748b; font-size:0.95rem;">
                            {{ $title == 'INICIAR SESIÓN' ? 'Accede a tu cuenta' : 'Únete a la plataforma de inventario del futuro' }}
                        </p>
                    </div>
                    <a href="{{ route('google.login') }}" id="googleLoginBtn"
                        class="btn w-100 mb-3 d-flex align-items-center justify-content-center bw-btn-google">
                        <span class="google-btn-content">
                            <i class="fab fa-google me-2" style="font-size:1.2rem; color:#4285F4;"></i> Continuar con Google
                        </span>
                        <span class="google-btn-spinner d-none">
                            <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                            Redirigiendo a Google...
                        </span>
                    </a>
                    <div class="separator">O</div>

                    {{ $slot }}

                    {{-- Enlaces cruzados login/register + home --}}
                    <div class="text-center mt-4 pt-3" style="border-top: 1px solid #e2e8f0;">
                        @if ($title == 'INICIAR SESIÓN')
                            <p class="mb-2" style="color:#64748b; font-size:0.9rem;">
                                ¿No tienes cuenta?
                                <a href="{{ route('register') }}" class="auth-link">Crear cuenta</a>
                            </p>
                        @else
                            <p class="mb-2" style="color:#64748b; font-size:0.9rem;">
                                ¿Ya tienes cuenta?
                                <a href="{{ route('login') }}" class="auth-link">Iniciar sesión</a>
                            </p>
                        @endif
                        <a href="{{ url('/') }}" class="auth-home-link">
                            <i class="fas fa-arrow-left me-1"></i> Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>