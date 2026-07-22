@extends('layouts.app')

@section('title', 'Dokumen - BPKA Scanner')
@section('header-title', 'Dokumen')
@section('header-subtitle', 'Kelola semua dokumen yang telah diupload')

@section('content')

@if (session('success'))
    <div class="card" style="border-color:var(--color-success); background:rgba(16,185,129,0.08); margin-bottom:1.5rem; color:var(--color-success); font-size:0.875rem;">
        {{ session('success') }}
    </div>
@endif

<!-- Filter -->
<div class="card library-card" style="margin-bottom:1.5rem;">
    <form action="{{ route('documents.index') }}" method="GET">
        <div class="library-filters">
            <div class="filter-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari dokumen...">
            </div>
            <div class="filter-dropdowns">
                <select name="category" class="custom-select-mini">
                    <option value="">Semua Kategori</option>
                    @foreach(['Kwitansi/Struk', 'Faktur/Invoice', 'Kontrak', 'Sertifikat', 'Pribadi', 'Kerja', 'Lainnya'] as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <select name="status" class="custom-select-mini">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            <a href="{{ route('documents.index') }}" class="btn btn-outline btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Document Table -->
@if($documents->isEmpty())
    <div class="card" style="text-align:center; padding:4rem 2rem;">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" style="margin:0 auto 1rem; color:var(--text-muted);"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
        <p class="text-muted" style="font-size:1rem;">Tidak ada dokumen ditemukan.</p>
    </div>
@else
    <div class="card table-card">
        <div class="table-responsive">
            <table class="doc-table">
                <thead>
                    <tr>
                        <th style="width:40px;">No</th>
                        <th>Nama Dokumen</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Ukuran</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="width:80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($documents as $i => $doc)
                        <tr>
                            <td class="text-muted">{{ $documents->firstItem() + $i }}</td>
                            <td>
                                <div class="doc-name-cell" onclick="window.location='{{ route('documents.show', $doc) }}'">
                                    <div class="doc-icon-container">
                                        @if(in_array($doc->file_type, ['jpg','jpeg','png']))
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        @endif
                                    </div>
                                    {{ $doc->title }}.{{ $doc->file_type }}
                                </div>
                            </td>
                            <td><span class="badge badge-{{ $doc->category }}">{{ $doc->category }}</span></td>
                            <td class="text-muted text-sm" style="text-transform:uppercase;">{{ $doc->file_type }}</td>
                            <td class="text-muted text-sm">{{ $doc->file_size_formatted }}</td>
                            <td class="text-muted text-sm">{{ $doc->created_at->format('d M Y') }}</td>
                            <td>
                                @if($doc->ocr_status === 'completed')
                                    <span class="ocr-status-badge badge-success">OCR Selesai</span>
                                @elseif($doc->ocr_status === 'processing')
                                    <span class="ocr-status-badge badge-active">Diproses</span>
                                @else
                                    <span class="ocr-status-badge badge-idle">Menunggu</span>
                                @endif
                            </td>
                            <td>
                                <div style="display:flex;gap:0.25rem;">
                                    <a href="{{ route('documents.show', $doc) }}" class="btn btn-outline btn-xs" title="Lihat">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </a>
                                    <form action="{{ route('documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('Yakin hapus?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-xs" title="Hapus">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div style="margin-top:1.5rem; text-align:center;">
        {{ $documents->withQueryString()->links() }}
    </div>
@endif

@endsection
