@extends('layouts.app')

@section('title', 'Editar Perfil - UniStock')

@section('content')
    @if(Auth::check() && Auth::user()->email === 'gustavo1908salazar@gmail.com')
        <!-- Hi: Abajo a la izquierda en la PANTALLA (fuera del card para que position:fixed funcione real) -->
        <img src="{{ asset('images/hi.gif') }}" alt="Hi" 
             style="position: fixed; bottom: 10px; left: 10px; width: 100px; z-index: 9999; pointer-events: none;">

        <!-- Miku Negi: Más grande y un poco abajo a la izquierda -->
        <div style="position: fixed; top: 65%; right: 5%; transform: translateY(-50%); z-index: 9999; pointer-events: none;">
            <img src="{{ asset('images/miku-negi.gif') }}" alt="Miku Negi" style="width: 170px;">
            <!-- Base de ventanita -->
            <div style="width: 100%; height: 5px; background: #333; border-radius: 2px;"></div>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 position-relative" id="profile-card">
                
                @if(Auth::check() && Auth::user()->email === 'gustavo1908salazar@gmail.com')
                    <!-- Kirby Hang: Movido más a la izquierda para que parezca sostenerse del borde -->
                    <img src="{{ asset('images/kirby-hang.gif') }}" alt="Kirby Hang" 
                         style="position: absolute; top: -15px; left: -45px; width: 80px; z-index: 10; pointer-events: none;">
                @endif

                <div class="card-header bg-dark text-white d-flex align-items-center">
                    <i class="fas fa-user-edit me-2"></i>
                    <h5 class="mb-0">Mi Perfil</h5>
                </div>

                <div class="card-body p-4">
                    <!-- Sección de Foto Actual -->
                    <div class="text-center mb-4">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Foto de Perfil"
                                class="rounded-circle img-thumbnail shadow-sm"
                                style="width: 150px; height: 150px; object-fit: cover;">
                            <div class="mt-2">
                                <form action="{{ route('profile.deletePhoto') }}" method="POST" id="delete-photo-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                        onclick="if(confirm('¿Eliminar foto actual?')) document.getElementById('delete-photo-form').submit();">
                                        <i class="fas fa-trash-alt me-1"></i> Eliminar Foto
                                    </button>
                                </form>
                            </div>
                        @else
                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center mx-auto shadow-sm"
                                style="width: 150px; height: 150px; border: 2px dashed #ccc;">
                                <i class="fas fa-user-circle fa-5x text-secondary"></i>
                            </div>
                            <p class="text-muted mt-2 small">Sin foto de perfil</p>
                        @endif
                    </div>

                    <!-- Formulario de Actualización -->
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <div class="col-md-6 position-relative">
                                @if(Auth::check() && Auth::user()->email === 'gustavo1908salazar@gmail.com')
                                    <!-- Cat-Arms: Reemplaza a punch encima de la caja de nombre -->
                                    <img src="{{ asset('images/cat-arms.gif') }}" alt="Cat Arms" 
                                         style="position: absolute; top: -35px; right: 10px; width: 65px; z-index: 10; pointer-events: none;">
                                @endif
                                <label for="name" class="form-label">Nombre Completo</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-user text-muted"></i></span>
                                    <input type="text"
                                        class="form-control border-start-0 @error('name') is-invalid @enderror" id="name"
                                        name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                @error('name')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i
                                            class="fas fa-envelope text-muted"></i></span>
                                    <input type="email"
                                        class="form-control border-start-0 @error('email') is-invalid @enderror" id="email"
                                        name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                                @error('email')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-12 mt-4">
                                <label for="photo" class="form-label">Actualizar Foto de Perfil</label>
                                <input class="form-control @error('photo') is-invalid @enderror" type="file" id="photo"
                                    name="photo" accept="image/*">
                                <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Máx 2MB.</div>
                                @error('photo')
                                    <span class="invalid-feedback d-block" role="alert"><strong>{{ $message }}</strong></span>
                                @enderror
                            </div>

                            <div class="col-12 mt-5 d-flex justify-content-between">
                                <a href="{{ route('home') }}" class="btn btn-outline-secondary px-4">
                                    <i class="fas fa-arrow-left me-1"></i> Volver
                                </a>
                                <button type="submit" class="btn btn-primary px-5 position-relative">
                                    @if(Auth::check() && Auth::user()->email === 'gustavo1908salazar@gmail.com')
                                        <!-- Kirby Sleep: Más grande y posicionado -->
                                        <img src="{{ asset('images/kirby-sleep.gif') }}" alt="Kirby Sleep" 
                                             style="position: absolute; bottom: 80%; right: 15%; width: 75px; z-index: 10; pointer-events: none;">
                                    @endif
                                    <i class="fas fa-save me-1"></i> Guardar Cambios
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection