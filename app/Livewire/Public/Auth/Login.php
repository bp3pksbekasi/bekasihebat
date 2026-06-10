<?php

declare(strict_types=1);

namespace App\Livewire\Public\Auth;

use App\Actions\AuthenticateUser;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Masuk - Bekasi Hebat')]
class Login extends Component
{
    public string $identifier = '';
    public string $password = '';
    public bool $remember = false;

    public function login(AuthenticateUser $action)
    {
        $this->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $success = $action->execute($this->identifier, $this->password, $this->remember);

        if (! $success) {
            $this->addError('identifier', 'Email/No. HP/NIA atau password salah.');

            return;
        }

        $dashboardRoute = $this->dashboardRoute();
        $intendedUrl = (string) session()->get('url.intended', '');

        if ($this->shouldIgnoreIntendedUrl($intendedUrl)) {
            session()->forget('url.intended');

            return redirect()->to($dashboardRoute);
        }

        return redirect()->intended($dashboardRoute);
    }

    public function render()
    {
        return view('livewire.public.auth.login');
    }

    private function dashboardRoute(): string
    {
        return route('member.dashboard');
    }

    private function shouldIgnoreIntendedUrl(string $intendedUrl): bool
    {
        if ($intendedUrl === '') {
            return false;
        }

        $appUrl = rtrim((string) config('app.url', url('/')), '/');
        $normalizedUrl = rtrim($intendedUrl, '/');

        return $normalizedUrl === $appUrl.'/admin'
            || str_ends_with($normalizedUrl, '/admin');
    }
}
