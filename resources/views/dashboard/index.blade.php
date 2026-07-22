@extends('layouts.app')

@section('title', 'Dashboard - BPKA Scanner')
@section('header-title', 'Dashboard')
@section('header-subtitle', 'Ringkasan sistem manajemen dokumen')

@section('content')

@if (session('success'))
    <div class="card" style="border-color:var(--color-success); background:rgba(16,185,129,0.08); margin-bottom:1.5rem; color:var(--color-success); font-size:0.875rem;">
        {{ session('success') }}
    </div>
@endif

<!-- Stats -->
<div class="stats-grid">
    <div class="card stat-card gradient-emerald border-glow">
        <div class="stat-header">
            <span class="stat-label">Total Dokumen</span>
            <div class="stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
        </div>
        <div class="stat-value" id="statTotal">{{ number_format($totalDocuments) }}</div>
        <div class="stat-footer text-muted">Semua dokumen yang terdaftar</div>
    </div>

    <div class="card stat-card border-glow">
        <div class="stat-header">
            <span class="stat-label">PDF</span>
            <div class="stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            </div>
        </div>
        <div class="stat-value" id="statPdf">{{ $pdfCount }}</div>
        <div class="stat-footer text-muted">File berformat PDF</div>
    </div>

    <div class="card stat-card border-glow">
        <div class="stat-header">
            <span class="stat-label">Gambar</span>
            <div class="stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2" ry="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
            </div>
        </div>
        <div class="stat-value" id="statImage">{{ $imageCount }}</div>
        <div class="stat-footer text-muted">File berformat gambar</div>
    </div>

    <div class="card stat-card border-glow">
        <div class="stat-header">
            <span class="stat-label">OCR Berhasil</span>
            <div class="stat-icon-wrapper">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
        </div>
        <div class="stat-value" id="statOcr" style="color:var(--color-success)">{{ $ocrRate }}%</div>
        <div class="stat-footer text-muted">Tingkat keberhasilan OCR</div>
    </div>
</div>

<!-- Toast -->
<div id="toast" style="position:fixed;top:1.5rem;right:1.5rem;z-index:9999;display:none;padding:1rem 1.5rem;border-radius:8px;font-size:0.875rem;font-weight:600;box-shadow:0 8px 32px rgba(0,0,0,0.3);transition:all 0.3s ease;"></div>

<!-- Upload + Kategori -->
<div class="dashboard-mid-grid" style="margin-top:1.5rem;">

    <!-- Upload -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Upload Dokumen</h3>
            <p class="card-desc text-muted">Drag & drop atau pilih file untuk diupload</p>
        </div>
        <div class="scanner-work-area" id="dropzone" style="min-height:220px; border:2px dashed var(--border-color); cursor:pointer; transition:all 0.3s ease;">
            <div class="dropzone" id="dropzoneContent">
                <svg xmlns="http://www.w3.org/2000/svg" class="dropzone-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <p style="font-weight:600; margin-bottom:0.25rem;">Seret & Lepas File</p>
                <p class="text-muted text-sm">PDF, JPG, PNG &middot; Maks 10MB</p>
                <input type="file" id="fileInput" accept=".pdf,.jpg,.jpeg,.png" style="display:none">
                <button type="button" class="btn btn-primary btn-sm" style="margin-top:1rem;" id="btnPickFile">Pilih File</button>
            </div>
        </div>
        <div id="fileList" style="margin-top:1rem;"></div>
        <div id="progressArea" style="display:none; margin-top:1rem;">
            <div style="display:flex;justify-content:space-between;font-size:0.8rem;margin-bottom:0.25rem;">
                <span id="progressLabel" class="text-muted">Mengunggah...</span>
                <span id="progressPercent" class="text-muted">0%</span>
            </div>
            <div style="width:100%;height:6px;background:var(--bg-tertiary);border-radius:3px;overflow:hidden;">
                <div id="progressBar" style="width:0%;height:100%;background:var(--accent-color);border-radius:3px;transition:width 0.3s ease;"></div>
            </div>
        </div>
        <div id="submitArea" style="display:none; margin-top:1rem; text-align:right;">
            <button type="button" class="btn btn-primary btn-glow" id="btnUpload">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                Upload Sekarang
            </button>
        </div>
    </div>

    <!-- Kategori -->
    <div class="card card-right">
        <div class="card-header">
            <h3 class="card-title">Kategori</h3>
            <p class="card-desc text-muted">Distribusi dokumen per kategori</p>
        </div>
        @if($categories->isEmpty())
            <p class="category-item-empty">Belum ada dokumen.</p>
        @else
            <div class="category-list">
                @foreach($categories as $cat)
                    <div class="category-item">
                        <div class="category-item-label">
                            <div class="category-item-dot" style="background:var(--accent-color);"></div>
                            {{ $cat->category }}
                        </div>
                        <span class="category-item-count">{{ $cat->count }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

</div>

<!-- Dokumen Terbaru -->
<div class="card table-card" style="margin-top:1.5rem;">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center;">
        <h3 class="card-title">Dokumen Terbaru</h3>
        <a href="{{ route('documents.index') }}" class="btn btn-outline btn-sm">Lihat Semua</a>
    </div>
    <div class="table-responsive">
        <table class="doc-table">
            <thead>
                <tr>
                    <th>Nama Dokumen</th>
                    <th>Kategori</th>
                    <th>Tanggal</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody id="recentDocsBody">
                @forelse($recentDocuments as $doc)
                    <tr onclick="window.location='{{ route('documents.show', $doc) }}'" style="cursor:pointer;">
                        <td>
                            <div class="doc-name-cell">
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
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted" style="padding:3rem;">Belum ada dokumen.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
const dropzone = document.getElementById('dropzone');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const submitArea = document.getElementById('submitArea');
const btnPickFile = document.getElementById('btnPickFile');
const btnUpload = document.getElementById('btnUpload');
const progressArea = document.getElementById('progressArea');
const progressBar = document.getElementById('progressBar');
const progressLabel = document.getElementById('progressLabel');
const progressPercent = document.getElementById('progressPercent');
const toast = document.getElementById('toast');

const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
let selectedFile = null;

function showToast(message, type) {
    toast.textContent = message;
    toast.style.display = 'block';
    toast.style.background = type === 'success' ? 'var(--color-success)' : 'var(--color-danger)';
    toast.style.color = '#fff';
    setTimeout(() => { toast.style.display = 'none'; }, 3000);
}

function formatSize(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(1) + ' MB';
    return (bytes / 1024).toFixed(1) + ' KB';
}

function handleFile(file) {
    const allowed = ['application/pdf', 'image/jpeg', 'image/png'];
    if (!allowed.includes(file.type)) {
        showToast('Format file tidak didukung.', 'error');
        return;
    }
    if (file.size > 10 * 1024 * 1024) {
        showToast('Ukuran file maksimal 10MB.', 'error');
        return;
    }
    selectedFile = file;
    fileList.innerHTML = '<div style="display:flex;align-items:center;gap:0.5rem;padding:0.5rem 0;border-bottom:1px solid var(--border-color);font-size:0.85rem;"><span style="color:var(--accent-color);">&#9679;</span> ' + file.name + ' <span class="text-muted text-xs">(' + formatSize(file.size) + ')</span></div>';
    submitArea.style.display = 'block';
}

btnPickFile.addEventListener('click', (e) => {
    e.stopPropagation();
    fileInput.click();
});

dropzone.addEventListener('click', () => fileInput.click());

dropzone.addEventListener('dragover', (e) => {
    e.preventDefault();
    dropzone.style.borderColor = 'var(--accent-color)';
    dropzone.style.background = 'rgba(16,185,129,0.05)';
});

dropzone.addEventListener('dragleave', () => {
    dropzone.style.borderColor = 'var(--border-color)';
    dropzone.style.background = 'transparent';
});

dropzone.addEventListener('drop', (e) => {
    e.preventDefault();
    dropzone.style.borderColor = 'var(--border-color)';
    dropzone.style.background = 'transparent';
    if (e.dataTransfer.files.length > 0) handleFile(e.dataTransfer.files[0]);
});

fileInput.addEventListener('change', () => {
    if (fileInput.files.length > 0) handleFile(fileInput.files[0]);
});

btnUpload.addEventListener('click', () => {
    if (!selectedFile) return;

    const formData = new FormData();
    formData.append('document', selectedFile);

    progressArea.style.display = 'block';
    submitArea.style.display = 'none';
    btnUpload.disabled = true;
    progressLabel.textContent = 'Mengunggah...';
    progressBar.style.width = '0%';

    fetch('{{ route("process-scan") }}', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': csrfToken }
    }).then(res => {
        if (!res.ok) throw res;
        return res.json();
    }).then(data => {
        progressBar.style.width = '100%';
        progressPercent.textContent = '100%';
        progressLabel.textContent = 'Berhasil!';
        showToast(data.message || 'Upload berhasil!', 'success');
        setTimeout(() => window.location.reload(), 1200);
    }).catch(err => {
        err.json().then(body => {
            showToast(body.message || 'Gagal mengunggah dokumen.', 'error');
        }).catch(() => showToast('Terjadi kesalahan.', 'error'));
        progressArea.style.display = 'none';
        submitArea.style.display = 'block';
        btnUpload.disabled = false;
    });
});
</script>

@endsection
