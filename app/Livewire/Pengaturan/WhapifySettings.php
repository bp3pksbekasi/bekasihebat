<?php

declare(strict_types=1);

namespace App\Livewire\Pengaturan;

use App\Models\AuditLog;
use App\Models\Setting;
use App\Services\WhapifyService;
use Livewire\Component;

class WhapifySettings extends Component
{
    public string $secret = '';
    public string $account = '';

    public string $testRecipient = '';
    public string $testMessage = 'Halo! Ini adalah pesan uji coba integrasi WhatsApp Whapify dari aplikasi Kabupaten Bekasi Hebat.';

    public string $statusMessage = '';
    public string $statusType = ''; // success, error

    public function mount(): void
    {
        abort_unless(auth()->user()?->isAdmin(), 403);

        $this->secret = Setting::get('whapify_secret', '');
        $this->account = Setting::get('whapify_account', '');
    }

    public function save(): void
    {
        $this->validate([
            'secret' => 'required|string|max:255',
            'account' => 'required|string|max:255',
        ]);

        Setting::set('whapify_secret', $this->secret);
        Setting::set('whapify_account', $this->account);

        AuditLog::log('update_settings', 'Update konfigurasi WhatsApp Whapify oleh ' . auth()->user()?->name);

        session()->flash('message', 'Konfigurasi Whapify berhasil disimpan.');
    }

    public function sendTest(): void
    {
        $this->validate([
            'testRecipient' => 'required|string|min:8|max:20',
            'testMessage' => 'required|string|min:1',
        ], [
            'testRecipient.required' => 'Nomor penerima uji coba wajib diisi.',
            'testMessage.required' => 'Pesan uji coba wajib diisi.',
        ]);

        $result = WhapifyService::sendMessage($this->testRecipient, $this->testMessage);

        if ($result['success']) {
            $this->statusType = 'success';
            $this->statusMessage = $result['message'] . (isset($result['response']) ? ' Detail: ' . json_encode($result['response']) : '');
        } else {
            $this->statusType = 'error';
            $this->statusMessage = $result['message'];
        }
    }

    public function render()
    {
        return view('livewire.pengaturan.whapify-settings')
            ->layout('components.layouts.app.sidebar');
    }
}
