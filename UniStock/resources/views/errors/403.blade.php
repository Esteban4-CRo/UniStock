@extends('layouts.app')

@section('title', 'Acceso Denegado - UniStock')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6 text-center">
            <div class="card shadow-sm border-0 p-5" style="border-radius: 20px;">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle"
                         style="width: 80px; height: 80px; background: rgba(37, 99, 235, 0.08);">
                        <i class="fas fa-lock" style="font-size: 2rem; color: #2563eb;"></i>
                    </div>
                </div>
                <h2 class="fw-bold mb-2" style="color: #1a1a2e;">Acceso Denegado</h2>
                <p class="text-muted mb-4" style="font-size: 1rem;">
                    {{ $exception->getMessage() ?: 'No tienes permisos para acceder a esta sección.' }}
                </p>
                <p class="text-muted mb-4" style="font-size: 0.9rem;">
                    Solo los usuarios con rol de <strong>Gerente</strong> o <strong>Super Usuario</strong> pueden acceder a esta función.
                    Contacta a tu administrador si crees que esto es un error.
                </p>
                <div class="d-flex gap-3 justify-content-center">
                    <a href="{{ route('home') }}" class="btn px-4 py-2" style="background: #1a1a2e; color: #fff; border-radius: 12px; font-weight: 600;">
                        <i class="fas fa-home me-2"></i> Ir al Inicio
                    </a>
                    <a href="{{ url()->previous() }}" class="btn px-4 py-2" style="background: #f0f4f8; color: #1a1a2e; border-radius: 12px; font-weight: 600; border: 1px solid #e2e8f0;">
                        <i class="fas fa-arrow-left me-2"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
