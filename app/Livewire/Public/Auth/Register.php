<?php

declare(strict_types=1);

namespace App\Livewire\Public\Auth;

use App\Actions\RegisterUser;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.public')]
#[Title('Gabung Komunitas - Bekasi Hebat')]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $password = '';
    public string $password_confirmation = '';
    public bool $agree = false;

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'phone' => ['required', 'string', 'min:10', 'max:20'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'agree' => ['accepted'],
        ];
    }

    public function register(RegisterUser $action)
    {
        $this->validate();

        try {
            $user = $action->execute([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => $this->password,
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Throwable) {
            $this->addError('email', 'Terjadi kesalahan. Coba lagi.');

            return;
        }

        auth()->login($user);
        session()->flash('welcome', "Selamat datang, {$user->name}!");

        return redirect()->route('member.dashboard');
    }

    public function render()
    {
        return view('livewire.public.auth.register');
    }
}
