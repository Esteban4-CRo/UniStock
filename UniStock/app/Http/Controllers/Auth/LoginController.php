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
        // Regenerate session id to prevent fixation (fast)
        $request->session()->regenerate();
        // Send Email Alert deferred to after response. Use fast-append to a
        // file and process in background to avoid DB queue insert (~300ms).
        $this->deferEmail($user);
    }

    private function deferEmail($user): void
    {
        $entry = json_encode([
            'email' => $user->email,
            'name' => $user->name,
            'time' => time(),
        ]) . "\n";
        $path = storage_path('app/login-alerts-queue.log');
        @file_put_contents($path, $entry, FILE_APPEND | LOCK_EX);

        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
        // Try to actually send the email right now, asynchronously-ish.
        register_shutdown_function(function () use ($user) {
            try {
                Mail::to($user->email)->send(new LoginAlertMail($user));
            } catch (\Exception $e) {
                \Log::error('No se pudo enviar el correo de alerta: ' . $e->getMessage());
            }
        });
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->stateless()
            ->redirectUrl(config('services.google.redirect'))
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->stateless()
                ->redirectUrl(config('services.google.redirect'))
                ->user();
            
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

            // Send Email Alert in background
            dispatch(function () use ($user) {
                try {
                    Mail::to($user->email)->send(new LoginAlertMail($user));
                } catch (\Exception $e) {
                    \Log::error('No se pudo enviar el correo de alerta: ' . $e->getMessage());
                }
            })->afterResponse();

            return redirect()->intended('/home');

        } catch (\Exception $e) {
            \Log::error('Error en Google Login: ' . $e->getMessage());
            return redirect('/login')->with('error', 'Error al iniciar sesión con Google: ' . $e->getMessage());
        }
    }
}
