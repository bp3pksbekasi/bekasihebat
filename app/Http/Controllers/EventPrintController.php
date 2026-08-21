<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EventPrintController extends Controller
{
    public function printLpj(Event $event)
    {
        // Pastikan hanya bisa dicetak jika disetujui atau selesai
        if (!in_array($event->status, [Event::STATUS_DISETUJUI, Event::STATUS_SELESAI])) {
            abort(403, 'LPJ hanya dapat dicetak untuk event yang sudah disetujui atau selesai.');
        }

        // Load relasi yang diperlukan
        $event->load([
            'budgetItems',
            'approvals.approver',
            'kegiatanRw',
            'creator'
        ]);

        $pdf = Pdf::loadView('pdf.events.lpj', compact('event'));

        // Set paper jika perlu
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('LPJ-' . \Illuminate\Support\Str::slug($event->judul) . '.pdf');
    }
}
