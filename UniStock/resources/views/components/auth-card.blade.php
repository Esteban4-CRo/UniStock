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
    /* Diseño en Blanco y Negro - Innovador y Elegante */
    body { background-color: #050505; color: #ffffff; font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; }
    .bw-card { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 20px; box-shadow: 0 25px 50px -12px rgba(0,0,0,1); overflow: hidden; }
    .bw-input { background: transparent !important; border: none !important; border-bottom: 1px solid rgba(255,255,255,0.3) !important; border-radius:0 !important; color:#fff !important; padding:0.75rem 0 !important; transition:all .3s ease !important; box-shadow:none !important; }
    .bw-input:focus { border-bottom-color:#fff !important; background:rgba(255,255,255,0.05) !important; padding-left:.5rem !important; }
    .bw-input::placeholder { color:rgba(255,255,255,0.4); }
    .bw-label { color:#aaaaaa; font-size:.8rem; text-transform:uppercase; letter-spacing:.1em; font-weight:600; margin-bottom:0; }
    .bw-btn { background:#fff; color:#000; font-weight:700; border-radius:30px; padding:1rem; border:none; transition:all .3s ease; text-transform:uppercase; letter-spacing:.1em; }
    .bw-btn:hover { background:#ccc; transform:translateY(-2px); }
    .bw-btn-google { background:transparent; color:#fff; border:1px solid rgba(255,255,255,0.3); border-radius:30px; padding:.75rem; font-weight:600; transition:all .3s; }
    .bw-btn-google:hover { background:rgba(255,255,255,0.1); border-color:#fff; }
    .separator { display:flex; align-items:center; text-align:center; color:rgba(255,255,255,0.4); margin:2rem 0; }
    .separator::before,.separator::after { content:''; flex:1; border-bottom:1px solid rgba(255,255,255,0.1); }
    .separator:not(:empty)::before { margin-right:1em; }
    .separator:not(:empty)::after { margin-left:1em; }
    .logo-container { display:flex; justify-content:center; align-items:center; margin-bottom:2rem; }
    .logo-container img { max-height:80px; filter:brightness(0) invert(1); }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="bw-card">
                <div class="card-body p-4 p-md-5">
                    <div class="logo-container">
                        <img src="{{ asset('images/1.gif') }}" alt="UniStock Logo" onerror="this.onerror=null; this.src=''; this.alt='[ LOGO ]'; this.style.color='white';">
                    </div>
                    <div class="text-center mb-5">
                        <h2 style="font-weight:800; letter-spacing:-1px;">{{ $title }}</h2>
                        <p style="color:#888;">
                            {{ $title == 'INICIAR SESIÓN' ? 'Accede a tu cuenta' : 'Únete a la plataforma de inventario del futuro' }}
                        </p>
                    </div>
                    <a href="{{ route('google.login') }}" class="btn w-100 mb-3 d-flex align-items-center justify-content-center bw-btn-google">
                        <i class="fab fa-google me-2" style="font-size:1.2rem;"></i> Continuar con Google
                    </a>
                    <div class="separator">O</div>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</div>
