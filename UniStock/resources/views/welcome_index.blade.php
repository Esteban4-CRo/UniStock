@extends('layouts.app')

@section('content')
    <div class="container py-5 mt-4">
        <!-- Hero Section -->
        <div class="row align-items-center mb-5 pb-4">
            <div class="col-lg-7 text-center text-lg-start mb-4 mb-lg-0">
                <h1 class="display-4 fw-bold mb-3 animate-soft-blur" style="color: var(--primary); letter-spacing: -1px;">
                    Gestión Inteligente
                    de Inventarios
                </h1>
                <p class="lead mb-4" style="color: var(--text); opacity: 0.85; max-width: 600px;">
                    UniStock centraliza el control de tus materias primas, proveedores y movimientos con una interfaz
                    rápida, segura y un asistente de Inteligencia Artificial integrado.
                </p>
                <div class="d-flex flex-column flex-sm-row justify-content-center justify-content-lg-start gap-3 mt-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg px-4 py-3 animate-spring-scale"
                            style="border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-rocket me-2"></i> Comenzar Ahora
                        </a>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-lg px-4 py-3 animate-spring-scale"
                            style="border-radius: 12px; font-weight: 600; animation-delay: 100ms;">
                            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
                        </a>
                    @else
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg px-4 py-3 animate-spring-scale"
                            style="border-radius: 12px; font-weight: 600;">
                            <i class="fas fa-tachometer-alt me-2"></i> Ir a mi Panel
                        </a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
                <!-- Espacio para ilustración si es necesario, o mantener limpio ya que el carrusel está arriba -->
                <div class="glass p-4 rounded-4 text-center d-flex flex-column justify-content-center"
                    style="min-height: 250px; border: 1px solid var(--border);">
                    <i class="fas fa-shield-alt mb-3" style="font-size: 3rem; color: var(--primary);"></i>
                    <h4 class="fw-bold">Sistema Seguro</h4>
                    <p class="small mb-0" style="opacity: 0.8;">Tus datos están protegidos en la nube con altos estándares
                        de seguridad y respaldos automáticos.</p>
                </div>
            </div>
        </div>

        <!-- Beneficios Grid -->
        <div class="row g-4 mt-2">
            <div class="col-md-4">
                <div class="card-base glass h-100 p-4 transition-card text-center text-md-start"
                    style="border-radius: 16px;">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 12px; background: var(--bot-bg); color: var(--primary);">
                            <i class="fas fa-box-open fs-4"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2">Control de Productos</h5>
                    <p class="small mb-0" style="opacity: 0.8; line-height: 1.6;">
                        Administre sus materias primas con códigos únicos e inmutables, controle existencias mínimas y
                        clasifique por ubicación.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-base glass h-100 p-4 transition-card text-center text-md-start"
                    style="border-radius: 16px;">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 12px; background: var(--bot-bg); color: var(--primary);">
                            <i class="fas fa-exchange-alt fs-4"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2">Movimientos Precisos</h5>
                    <p class="small mb-0" style="opacity: 0.8; line-height: 1.6;">
                        Registre entradas y salidas con trazabilidad total. Conozca al detalle qué proveedor suministró el
                        material y hacia dónde fue despachado.
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card-base glass h-100 p-4 transition-card text-center text-md-start"
                    style="border-radius: 16px;">
                    <div class="mb-3">
                        <div class="d-inline-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; border-radius: 12px; background: var(--bot-bg); color: var(--primary);">
                            <i class="fas fa-robot fs-4"></i>
                        </div>
                    </div>
                    <h5 class="fw-bold mb-2">Asistente IA</h5>
                    <p class="small mb-0" style="opacity: 0.8; line-height: 1.6;">
                        Tome decisiones informadas con la ayuda de nuestro chatbot inteligente que analiza el estado de su
                        almacén en tiempo real.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        .transition-card {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s ease;
        }

        .transition-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
            border-color: var(--primary) !important;
        }
    </style>
@endsection