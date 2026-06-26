<?php

namespace App\Livewire\Pengaturan;

use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Api extends Component
{
    public string $openaiApiKey = '';

    public function mount()
    {
        $this->openaiApiKey = DB::table('settings')->where('key', 'openai_api_key')->value('value') ?? '';
    }

    public function save()
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'openai_api_key'],
            ['value' => $this->openaiApiKey, 'updated_at' => now()]
        );

        session()->flash('success', 'API Key berhasil disimpan ke database.');
    }

    public function render()
    {
        return view('livewire.pengaturan.api')->layout('components.layouts.app', ['title' => 'Pengaturan API']);
    }
}
