@php
    $pageTitle = 'Edit Program';
    $pageSubtitle = 'Update data program kegiatan';
    $submitDraftLabel = 'Simpan Draft';
    $submitApprovalLabel = 'Update & Ajukan Approval';
    $existingCover = $event->cover_image ?? null;
@endphp

@include('livewire.events.form')
