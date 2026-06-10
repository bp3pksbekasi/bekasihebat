<?php

namespace App\Filament\Pages;

class Dashboard extends \Filament\Pages\Dashboard
{
    public function mount(): void
    {
        redirect('/dashboard');
    }
}
