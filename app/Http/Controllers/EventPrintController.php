<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EventPrintController extends Controller
{
    public function printLpj($event)
    {
        $eventModel = $event instanceof Event
            ? $event
            : Event::where('uuid', $event)->orWhere('id', $event)->firstOrFail();

        $user = auth()->user();
        $allowedStatuses = [
            Event::STATUS_DISETUJUI,
            Event::STATUS_BERLANGSUNG,
            Event::STATUS_SELESAI,
            'approved',
            'completed',
            'ongoing',
            'berlangsung',
            'disetujui',
            'selesai',
        ];

        // Pastikan hanya bisa dicetak jika disetujui, berlangsung, atau selesai (atau admin)
        if (!in_array($eventModel->status, $allowedStatuses, true) && !$user?->isAdmin()) {
            abort(403, 'LPJ hanya dapat dicetak untuk event yang sudah disetujui, berlangsung, atau selesai.');
        }

        // Load relasi yang diperlukan
        $eventModel->load([
            'budgetItems',
            'approvals.approver',
            'kegiatanRw',
            'creator',
            'report.creator',
            'bidang',
        ]);

        $pdf = Pdf::loadView('pdf.events.lpj', ['event' => $eventModel]);

        // Set paper
        $pdf->setPaper('A4', 'portrait');

        return $pdf->stream('LPJ-' . \Illuminate\Support\Str::slug($eventModel->judul ?: 'program') . '.pdf');
    }
}
