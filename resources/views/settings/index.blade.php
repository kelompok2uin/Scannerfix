@extends('layouts.app')

@section('title', 'Pengaturan - BPKA Scanner')
@section('header-title', 'Pengaturan')
@section('header-subtitle', 'Konfigurasi aplikasi BPKA Scanner')

@section('content')

@if (session('success'))
    <div class="card" style="border-color:var(--color-success); background:rgba(16,185,129,0.08); margin-bottom:1.5rem; color:var(--color-success); font-size:0.875rem; padding:0.75rem 1rem;">
        {{ session('success') }}
    </div>
@endif

<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; max-width:900px;">

    <!-- Info Aplikasi -->
    <div class="card" style="padding:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Info Aplikasi</h3>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Nama Aplikasi</span>
                <span style="font-weight:600;">{{ $settings['app_name'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Environment</span>
                <span class="badge badge-emerald">{{ $settings['app_env'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Debug Mode</span>
                <span style="font-weight:600;color:{{ $settings['app_debug'] ? 'var(--color-success)' : 'var(--color-danger)' }}">{{ $settings['app_debug'] ? 'Aktif' : 'Nonaktif' }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">URL</span>
                <span style="font-weight:600;">{{ $settings['app_url'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Versi</span>
                <span class="badge badge-Kerja">v1.0.0</span>
            </div>
        </div>
    </div>

    <!-- Konfigurasi -->
    <div class="card" style="padding:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Konfigurasi</h3>
        <form action="{{ route('settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label>Nama Aplikasi</label>
                <input type="text" name="app_name" class="form-control" value="{{ $settings['app_name'] }}" style="font-size:0.85rem;">
            </div>
            <div class="form-group">
                <label>Timezone</label>
                <select name="timezone" class="form-control" style="font-size:0.85rem;">
                    @foreach(['UTC', 'Asia/Jakarta', 'Asia/Makassar', 'Asia/Jayapura'] as $tz)
                        <option value="{{ $tz }}" {{ $settings['timezone'] === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label>Bahasa</label>
                <select name="locale" class="form-control" style="font-size:0.85rem;">
                    <option value="id" {{ $settings['locale'] === 'id' ? 'selected' : '' }}>Indonesia</option>
                    <option value="en" {{ $settings['locale'] === 'en' ? 'selected' : '' }}>English</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-glow w-full" style="justify-content:center;">Simpan Pengaturan</button>
        </form>
    </div>

    <!-- Database -->
    <div class="card" style="padding:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Database</h3>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Driver</span>
                <span class="badge badge-Faktur/Invoice">{{ strtoupper($settings['db_connection']) }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Total Users</span>
                <span style="font-weight:600;">{{ \App\Models\User::count() }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Total Dokumen</span>
                <span style="font-weight:600;">{{ \App\Models\Document::count() }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Ukuran Database</span>
                <span style="font-weight:600;">{{ file_exists(database_path('database.sqlite')) ? round(filesize(database_path('database.sqlite')) / 1024, 1) . ' KB' : 'N/A' }}</span>
            </div>
        </div>
    </div>

    <!-- Scanner Info -->
    <div class="card" style="padding:1.5rem;">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Scanner</h3>
        <div style="display:flex;flex-direction:column;gap:0.75rem;">
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Max Upload</span>
                <span style="font-weight:600;">{{ $settings['max_upload_size'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Format Didukung</span>
                <span style="font-weight:600;">{{ $settings['supported_formats'] }}</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">OCR Engine</span>
                <span class="badge badge-Sertifikat">Tesseract.js</span>
            </div>
            <div style="display:flex;justify-content:space-between;font-size:0.85rem;">
                <span class="text-muted">Export</span>
                <span style="font-weight:600;">PDF, JPG, PNG, TXT</span>
            </div>
        </div>
    </div>

    <!-- Storage -->
    <div class="card" style="padding:1.5rem; grid-column: span 2;">
        <h3 style="font-size:1rem;font-weight:700;margin-bottom:1rem;">Penyimpanan</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:1rem;">
            <div style="background:var(--bg-tertiary);padding:1rem;border-radius:8px;border:1px solid var(--border-color);text-align:center;">
                <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;">Dokumen Tersimpan</p>
                <p style="font-size:1.5rem;font-weight:800;color:var(--accent-color);">{{ \App\Models\Document::count() }}</p>
            </div>
            <div style="background:var(--bg-tertiary);padding:1rem;border-radius:8px;border:1px solid var(--border-color);text-align:center;">
                <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;">OCR Selesai</p>
                <p style="font-size:1.5rem;font-weight:800;color:var(--color-success);">{{ \App\Models\Document::where('ocr_status', 'completed')->count() }}</p>
            </div>
            <div style="background:var(--bg-tertiary);padding:1rem;border-radius:8px;border:1px solid var(--border-color);text-align:center;">
                <p style="font-size:0.75rem;color:var(--text-muted);margin-bottom:0.5rem;">Menunggu Proses</p>
                <p style="font-size:1.5rem;font-weight:800;color:var(--color-warning);">{{ \App\Models\Document::where('ocr_status', 'pending')->count() }}</p>
            </div>
        </div>
    </div>

</div>

@endsection
