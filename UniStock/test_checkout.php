<?php

use Illuminate\Support\Facades\Http;
use App\Models\User;

// Simulate hitting endpoints
$response = Http::post('http://127.0.0.1:8000/register', [
    'name' => 'Manuel Prueba',
    'email' => 'manuel@prueba.com',
    'password' => 'password',
    'password_confirmation' => 'password',
    'role' => 'almacenista',
    '_token' => csrf_token() // Need csrf. Actually easier to test directly via facade or request
]);

