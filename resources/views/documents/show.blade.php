@extends('layouts.app')

@section('title', $document->title . ' - BPKA Scanner')
@section('header-title', $document->title . '.' . $document->file_type)
@section('header-subtitle', 'Detail dokumen')

@section('content')

<div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
    <a href="{{ route('documents.index') }}" class="btn btn-outline btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m15 18-6-6 6-6"/></svg>
        Kembali
    </a>
    <div style="flex-grow:1;"></div>
    <a href="{{ route('documents.download', $document) }}" class="btn btn-primary btn-sm">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
        Download
    </a>
    <form action="{{ route('documents.destroy', $document) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus dokumen ini?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-outline-danger btn-sm">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
            Hapus
        </button>
    </form>
</div>

<div class="doc-modal-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem;">

    <!-- Preview -->
    <div class="card" style="padding:0; overflow:hidden;">
        <div class="modal-doc-preview" style="border-radius:12px; min-height:500px;">
            @if(in_array($document->file_type, ['jpg', 'jpeg', 'png']))
                <img src="{{ asset('storage/' . $document->file_path) }}" alt="{{ $document->title }}">
            @elseif($document->file_type === 'pdf')
                <iframe src="{{ asset('storage/' . $document->file_path) }}" style="width:100%; height:100%; min-height:500px; border:none;"></iframe>
            @else
                <div class="text-muted" style="padding:3rem; text-align:center;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 1rem;"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <p>Preview tidak tersedia</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Detail -->
    <div style="display:flex; flex-direction:column; gap:1.5rem;">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title" style="color:var(--accent-color); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Informasi Dokumen</h3>
            </div>
            <div class="details-meta-grid" style="background:var(--bg-tertiary); border-radius:8px; padding:1rem; display:grid; grid-template-columns:1fr 1fr; gap:1rem;">
                <div class="meta-item">
                    <span class="meta-item-label">Judul</span>
                    <span class="meta-item-value">{{ $document->title }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-item-label">Tipe File</span>
                    <span class="meta-item-value" style="text-transform:uppercase;">{{ $document->file_type }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-item-label">Ukuran</span>
                    <span class="meta-item-value">{{ $document->file_size_formatted }}</span>
                </div>
                <div class="meta-item">
                    <span class="meta-item-label">Kategori</span>
                    <span class="meta-item-value"><span class="badge badge-{{ $document->category }}">{{ $document->category }}</span></span>
                </div>
                <div class="meta-item">
                    <span class="meta-item-label">Status OCR</span>
                    <span class="meta-item-value">
                        @if($document->ocr_status === 'completed')
                            <span class="ocr-status-badge badge-success">Selesai</span>
                        @elseif($document->ocr_status === 'processing')
                            <span class="ocr-status-badge badge-active">Diproses</span>
                        @else
                            <span class="ocr-status-badge badge-idle">Menunggu</span>
                        @endif
                    </span>
                </div>
                <div class="meta-item">
                    <span class="meta-item-label">Tanggal Upload</span>
                    <span class="meta-item-value">{{ $document->created_at->format('d M Y H:i') }}</span>
                </div>
            </div>
        </div>

        <div class="card" style="flex-grow:1; display:flex; flex-direction:column;">
            <div class="card-header">
                <h3 class="card-title" style="color:var(--accent-color); font-size:0.85rem; text-transform:uppercase; letter-spacing:0.05em;">Hasil OCR</h3>
            </div>
            @if($document->ocr_text)
                <div class="extracted-text-block" style="flex-grow:1;">
                    <textarea class="form-control" readonly style="flex-grow:1; min-height:200px; font-size:0.85rem; line-height:1.6; resize:none;">{{ $document->ocr_text }}</textarea>
                </div>
            @else
                <div class="ocr-section-card" style="text-align:center; padding:2.5rem;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 0.75rem; color:var(--text-muted);"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                    <p class="text-muted text-sm">Belum ada hasil OCR</p>
                </div>
            @endif
        </div>

    </div>
</div>

@endsection
