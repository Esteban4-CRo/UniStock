<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoginAlertMail;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function authenticated(\Illuminate\Http\Request $request, $user)
    {
        // Send Email Alert on normal login too
        try {
            Mail::to($user->email)->send(new LoginAlertMail($user));
        } catch (\Exception $e) {
            \Log::error('No se pudo enviar el correo de alerta: ' . $e->getMessage());
        }
    }

    public function redirectToGoogle()
{
    // Initiate Google OAuth flow using configured redirect URI
    return Socialite::driver('google')
        ->stateless()
        ->redirect();
}

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Check if email already exists to prevent duplicates
            $user = User::where('email', $googleUser->email)->first();

            if (!$user) {
                // Register new user
                $user = User::create([
                    'name' => $googleUser->name,
                    'email' => $googleUser->email,
                    'password' => Hash::make(Str::random(24)),
                    'role' => User::ROLE_ALMACENISTA, // Default role
                    'activo' => true,
                    'photo' => $googleUser->avatar, // Save Google profile picture
                ]);
            } else {
                // Update photo if it's missing
                if (empty($user->photo) && !empty($googleUser->avatar)) {
                    $user->photo = $googleUser->avatar;
                    $user->save();
                }
            }

            Auth::login($user, true);

            // Send Email Alert
            try {
                Mail::to($user->email)->send(new LoginAlertMail($user));
            } catch (\Exception $e) {
                \Log::error('No se pudo enviar el correo de alerta: ' . $e->getMessage());
            }

            return redirect()->intended($this->redirectTo);

        } catch (\Exception $e) {
            \Log::error('Error en Google Login: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Error al iniciar sesión con Google: Verifique sus credenciales (Client Secret).');
        }
    }
}
