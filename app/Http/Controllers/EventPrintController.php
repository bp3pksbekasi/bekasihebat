<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EventPrintController extends Controller
{
    public function printLpj(Event $event): \Illuminate\Http\Response
    {
        $eventModel = $event;

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
        $pdf->render();
        $this->addPageNumbers($pdf->getDomPDF());

        return $pdf->stream('LPJ-' . \Illuminate\Support\Str::slug($eventModel->judul ?: 'program') . '.pdf');
    }

    /**
     * Cetak "Halaman X dari Y" di pojok kanan bawah setiap halaman,
     * karena total halaman baru diketahui setelah PDF selesai dirender.
     */
    private function addPageNumbers(\Dompdf\Dompdf $dompdf): void
    {
        $canvas = $dompdf->getCanvas();
        $font = $dompdf->getFontMetrics()->getFont('DejaVu Sans');

        $canvas->page_text(
            $canvas->get_width() - 51 - 62,
            $canvas->get_height() - 44,
            'Halaman {PAGE_NUM} dari {PAGE_COUNT}',
            $font,
            6,
            [0.61, 0.64, 0.69],
        );
    }
}
