@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div id="welcomeCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="d-flex align-items-center justify-content-center" style="height:60vh; background:#fff;">
                        <div class="text-center">
                            <h1 style="font-weight:800; font-size:2.4rem;">UniStock</h1>
                            <p style="font-size:1.1rem; color:#555; max-width:700px; margin:0 auto;">Gestión simple de inventarios — registrese o inicie sesión para continuar.</p>
                            <div class="mt-4">
                                <a href="{{ route('register') }}" class="btn btn-primary me-2"><i class="fas fa-user-plus"></i> Registrarse</a>
                                <a href="{{ route('login') }}" class="btn btn-secondary"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="carousel-item">
                    <div class="d-flex align-items-center justify-content-center" style="height:60vh; background:#f8f8f8;">
                        <div class="text-center">
                            <h2 style="font-weight:700;">Funcionalidades</h2>
                            <p style="color:#555; max-width:700px; margin:0 auto;">Cree productos con códigos únicos, gestione entradas y salidas, y controle el inventario desde un panel sencillo y monocromo.</p>
                            <div class="mt-4">
                                <a href="{{ route('register') }}" class="btn btn-primary me-2">Comenzar</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#welcomeCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Siguiente</span>
            </button>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Productos</h5>
                <p class="card-text">Administre productos con códigos únicos e inmutables.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Entradas / Salidas</h5>
                <p class="card-text">Registre movimientos y lleve control del stock.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title">Usuarios</h5>
                <p class="card-text">Gestione usuarios y perfiles con foto.</p>
            </div>
        </div>
    </div>
</div>

@endsection
