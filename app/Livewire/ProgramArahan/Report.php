<?php

declare(strict_types=1);

namespace App\Livewire\ProgramArahan;

use App\Models\ProgramArahan;
use App\Models\ProgramArahanReport;
use Livewire\Component;
use Livewire\WithFileUploads;

class Report extends Component
{
    use WithFileUploads;

    public ProgramArahan $program;

    public string $ringkasan = '';
    public int $jumlahKorwe = 0;
    public int $jumlahKorte = 0;
    public int $jumlahPenggalang = 0;
    public string $evaluasi = '';
    public string $tindakLanjut = '';
    public $foto = [];
    public string $realisasiAnggaran = '0';
    public string $rating = '';

    public function mount(ProgramArahan $programArahan)
    {
        $this->program = $programArahan;
        if ($this->program->report) {
            $this->ringkasan = $this->program->report->ringkasan ?? '';
            $this->jumlahKorwe = $this->program->report->jumlah_korwe_terbentuk;
            $this->jumlahKorte = $this->program->report->jumlah_korte_terbentuk;
            $this->jumlahPenggalang = $this->program->report->jumlah_penggalang_terekrut;
            $this->evaluasi = $this->program->report->evaluasi ?? '';
            $this->tindakLanjut = $this->program->report->tindak_lanjut ?? '';
            $this->realisasiAnggaran = (string) $this->program->report->realisasi_anggaran;
            $this->rating = $this->program->report->rating ?? '';
        } else {
            $this->ringkasan = $this->program->judul;
        }
    }

    public function save()
    {
        $this->validate([
            'ringkasan' => 'required|string',
            'jumlahKorwe' => 'integer|min:0',
            'jumlahKorte' => 'integer|min:0',
            'jumlahPenggalang' => 'integer|min:0',
            'evaluasi' => 'nullable|string',
            'tindakLanjut' => 'nullable|string',
            'foto.*' => 'image|max:4096',
            'realisasiAnggaran' => 'nullable|numeric|min:0',
            'rating' => 'nullable|string',
        ]);

        $fotoPaths = [];
        if ($this->program->report && $this->program->report->foto) {
            $fotoPaths = $this->program->report->foto;
        }

        foreach ($this->foto as $file) {
            $fotoPaths[] = $file->store('program_arahan_reports', 'public');
        }

        ProgramArahanReport::updateOrCreate(
            ['program_arahan_id' => $this->program->id],
            [
                'ringkasan' => $this->ringkasan,
                'jumlah_korwe_terbentuk' => $this->jumlahKorwe,
                'jumlah_korte_terbentuk' => $this->jumlahKorte,
                'jumlah_penggalang_terekrut' => $this->jumlahPenggalang,
                'evaluasi' => $this->evaluasi ?: null,
                'tindak_lanjut' => $this->tindakLanjut ?: null,
                'foto' => $fotoPaths ?: null,
                'realisasi_anggaran' => (float)$this->realisasiAnggaran,
                'rating' => $this->rating ?: null,
                'created_by' => auth()->id(),
            ]
        );

        $this->program->update(['status' => 'selesai']);

        session()->flash('message', 'Laporan program berhasil disimpan.');
        
        return redirect()->route('buku-induk-rw.detail', [
            'profilRw' => $this->program->nomor_rw, 
            'desa' => $this->program->targetWilayah->desa
        ]);
    }

    public function render()
    {
        return view('livewire.program-arahan.report')
            ->layout('components.layouts.app-fullwidth', ['title' => 'Laporan Program Arahan']);
    }
}
