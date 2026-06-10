<?php

namespace App\Filament\Pages\Auth;

use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;

class Login extends BaseLogin
{
    // Point to our custom Blade view
    protected string $view = 'filament.pages.auth.login';

    protected static string $layout = 'filament-panels::components.layout.base';

    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();
        $credentials = $this->getCredentialsFromFormData($data);

        // Check if user exists in the system
        $user = \App\Models\User::where('email', $credentials['email'])->first();

        if (!$user) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Email / User tidak ditemukan di sistem.',
                'data.email' => 'Email / User tidak ditemukan di sistem.',
            ]);
        }

        // Set the session variable BEFORE attempt so canAccessPanel check passes!
        session(['logged_in_via_admin' => true]);

        if (! Filament::auth()->attempt($credentials, $data['remember'] ?? false)) {
            session()->forget('logged_in_via_admin');

            throw \Illuminate\Validation\ValidationException::withMessages([
                'email' => 'Password yang Anda masukkan salah.',
                'data.email' => 'Password yang Anda masukkan salah.',
            ]);
        }

        return new class implements LoginResponse {
            public function toResponse($request)
            {
                return redirect()->to('/dashboard');
            }
        };
    }
}
