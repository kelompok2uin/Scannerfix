@extends('layouts.app')

@section('title', 'Scanner - BPKA Scanner')
@section('header-title', 'Pemindaian Dokumen')
@section('header-subtitle', 'Upload, edit, dan simpan dokumen')

@section('content')

<style>
.scanner-layout{display:grid;grid-template-columns:1fr 360px;gap:1.5rem;height:calc(100vh - 200px);}
.scanner-main{display:flex;flex-direction:column;gap:1rem;min-height:0;}
.scanner-tools{display:flex;flex-direction:column;gap:1rem;overflow-y:auto;max-height:calc(100vh - 200px);}
.canvas-area{position:relative;background:#111;border-radius:12px;overflow:hidden;flex:1;min-height:350px;display:flex;align-items:center;justify-content:center;}
.canvas-area canvas{max-width:100%;max-height:100%;}
.upload-zone{display:flex;flex-direction:column;align-items:center;justify-content:center;cursor:pointer;width:100%;height:100%;position:absolute;inset:0;}
.upload-zone.dragover{background:rgba(16,185,129,0.05);}
.filter-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:0.5rem;}
.filter-btn{padding:0.5rem;border:1px solid var(--border-color);border-radius:8px;background:var(--bg-tertiary);color:var(--text-secondary);font-size:0.75rem;font-weight:600;cursor:pointer;transition:all 0.2s;text-align:center;}
.filter-btn:hover{border-color:var(--accent-color);color:var(--text-primary);}
.filter-btn.active{background:var(--accent-color);color:#fff;border-color:var(--accent-color);}
.slider-row{display:flex;align-items:center;gap:0.5rem;}
.slider-row label{font-size:0.75rem;font-weight:600;color:var(--text-secondary);min-width:65px;}
.slider-row input[type=range]{flex:1;-webkit-appearance:none;height:4px;border-radius:2px;background:var(--bg-tertiary);outline:none;}
.slider-row input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:14px;height:14px;border-radius:50%;background:var(--accent-color);cursor:pointer;}
.slider-row .val{font-size:0.7rem;color:var(--text-muted);min-width:28px;text-align:right;}
.batch-thumbs{display:flex;gap:0.5rem;overflow-x:auto;padding:0.5rem 0;}
.batch-thumb{width:60px;height:60px;border-radius:6px;border:2px solid var(--border-color);overflow:hidden;cursor:pointer;flex-shrink:0;position:relative;}
.batch-thumb.active{border-color:var(--accent-color);}
.batch-thumb img{width:100%;height:100%;object-fit:cover;}
.batch-thumb .remove{position:absolute;top:-4px;right:-4px;width:18px;height:18px;border-radius:50%;background:var(--color-danger);color:#fff;font-size:11px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;line-height:1;}
.ocr-result{background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:8px;padding:0.75rem;min-height:80px;max-height:180px;overflow-y:auto;font-size:0.8rem;line-height:1.6;color:var(--text-secondary);}
.progress-ocr{height:4px;background:var(--bg-tertiary);border-radius:2px;overflow:hidden;margin-top:0.5rem;}
.progress-ocr-fill{height:100%;background:var(--accent-color);border-radius:2px;transition:width 0.3s;width:0;}
.export-options{display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;}
.export-btn{display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.6rem;border:1px solid var(--border-color);border-radius:8px;background:var(--bg-secondary);color:var(--text-primary);font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;}
.export-btn:hover{border-color:var(--accent-color);background:var(--accent-soft);}
.translate-box{background:var(--bg-secondary);border:1px solid var(--border-color);border-radius:8px;padding:0.75rem;font-size:0.8rem;line-height:1.6;color:var(--text-secondary);min-height:50px;display:none;}
.quick-actions{display:flex;gap:0.5rem;margin-bottom:1rem;}
.quick-btn{flex:1;display:flex;align-items:center;justify-content:center;gap:0.5rem;padding:0.6rem;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer;transition:all 0.2s;border:none;}
.quick-btn-save{background:var(--color-success);color:#fff;}
.quick-btn-save:hover{opacity:0.85;}
.quick-btn-delete{background:transparent;border:1px solid rgba(239,68,68,0.3);color:var(--color-danger);}
.quick-btn-delete:hover{background:rgba(239,68,68,0.1);}
.quick-btn-export{background:var(--bg-tertiary);border:1px solid var(--border-color);color:var(--text-primary);}
.quick-btn-export:hover{border-color:var(--accent-color);}
.toast{position:fixed;bottom:2rem;right:2rem;padding:0.75rem 1.25rem;border-radius:8px;font-size:0.85rem;font-weight:600;z-index:9999;transform:translateY(100px);opacity:0;transition:all 0.3s;}
.toast.show{transform:translateY(0);opacity:1;}
.toast.success{background:var(--color-success);color:#fff;}
.toast.error{background:var(--color-danger);color:#fff;}
.toast.info{background:var(--color-info);color:#fff;}
@media(max-width:1024px){.scanner-layout{grid-template-columns:1fr;height:auto;}.scanner-tools{max-height:none;}.canvas-area{min-height:300px;}}
</style>

<div class="scanner-layout">

    <!-- Main Area -->
    <div class="scanner-main">

        <!-- Quick Actions -->
        <div class="quick-actions" id="quickActions" style="display:none;">
            <button class="quick-btn quick-btn-save" onclick="quickSave()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                Simpan Cepat
            </button>
            <button class="quick-btn quick-btn-export" onclick="exportAs('pdf')">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export PDF
            </button>
            <button class="quick-btn quick-btn-delete" onclick="clearCanvas()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                Hapus
            </button>
        </div>

        <div class="canvas-area" id="canvasArea">
            <canvas id="editCanvas"></canvas>
            <div class="upload-zone" id="uploadZone" onclick="document.getElementById('fileUploadInput').click()">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="color:var(--text-muted);margin-bottom:1rem;transition:transform 0.3s;" id="uploadIcon"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                <p style="font-weight:600;color:var(--text-primary);margin-bottom:0.25rem;">Klik atau seret file ke sini</p>
                <p class="text-muted text-sm">JPG, PNG, PDF &middot; Maks 10MB &middot; Bisa multi-file</p>
                <input type="file" id="fileUploadInput" multiple accept="image/*,.pdf" style="display:none">
            </div>
        </div>

        <!-- Batch Thumbnails -->
        <div id="batchSection" style="display:none;">
            <div class="batch-thumbs" id="batchThumbs"></div>
            <div style="font-size:0.7rem;color:var(--text-muted);text-align:center;padding-top:0.25rem;" id="batchCount">0 halaman</div>
        </div>

    </div>

    <!-- Tools Sidebar -->
    <div class="scanner-tools">

        <!-- Filter -->
        <div class="card" style="padding:1rem;">
            <h4 style="font-size:0.85rem;font-weight:700;margin-bottom:0.75rem;">Filter & Koreksi</h4>
            <div class="filter-grid">
                <button class="filter-btn active" id="fOriginal" onclick="applyFilter('original')">Original</button>
                <button class="filter-btn" id="fBw" onclick="applyFilter('bw')">B&W</button>
                <button class="filter-btn" id="fGrayscale" onclick="applyFilter('grayscale')">Grayscale</button>
                <button class="filter-btn" id="fMagic" onclick="applyFilter('magic')">Magic</button>
                <button class="filter-btn" id="fContrast" onclick="applyFilter('contrast')">Kontras</button>
                <button class="filter-btn" id="fSharp" onclick="applyFilter('sharp')">Tajam</button>
            </div>
            <div style="margin-top:0.75rem;display:flex;flex-direction:column;gap:0.5rem;">
                <div class="slider-row">
                    <label>Kecerahan</label>
                    <input type="range" id="brightness" min="-100" max="100" value="0" oninput="updateImage()">
                    <span class="val" id="brightnessVal">0</span>
                </div>
                <div class="slider-row">
                    <label>Kontras</label>
                    <input type="range" id="contrast" min="-100" max="100" value="0" oninput="updateImage()">
                    <span class="val" id="contrastVal">0</span>
                </div>
                <div class="slider-row">
                    <label>Saturasi</label>
                    <input type="range" id="saturation" min="-100" max="100" value="0" oninput="updateImage()">
                    <span class="val" id="saturationVal">0</span>
                </div>
            </div>
            <div style="margin-top:0.75rem;display:flex;gap:0.5rem;">
                <button class="btn btn-outline btn-sm" style="flex:1;" onclick="rotateImage(-90)">&#8634; Kiri</button>
                <button class="btn btn-outline btn-sm" style="flex:1;" onclick="rotateImage(90)">&#8635; Kanan</button>
            </div>
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem;">
                <button class="btn btn-outline btn-sm" style="flex:1;" onclick="autoCrop()">Auto Crop</button>
                <button class="btn btn-outline btn-sm" style="flex:1;" onclick="perspectiveFix()">Perbaiki Sudut</button>
            </div>
        </div>

        <!-- OCR -->
        <div class="card" style="padding:1rem;">
            <h4 style="font-size:0.85rem;font-weight:700;margin-bottom:0.75rem;">OCR - Ekstraksi Teks</h4>
            <button class="btn btn-primary btn-sm w-full" style="justify-content:center;" onclick="runOCR()">Jalankan OCR</button>
            <div class="progress-ocr" id="ocrProgress" style="display:none;"><div class="progress-ocr-fill" id="ocrProgressFill"></div></div>
            <div class="ocr-result" id="ocrResult" style="margin-top:0.75rem;">
                <span class="text-muted">Hasil OCR muncul di sini...</span>
            </div>
            <div style="margin-top:0.5rem;display:flex;gap:0.5rem;">
                <button class="btn btn-outline btn-sm" style="flex:1;" onclick="copyOCRText()">Salin</button>
                <button class="btn btn-outline btn-sm" style="flex:1;" onclick="translateOCRText()">Terjemahkan</button>
            </div>
            <div class="translate-box" id="translateResult"></div>
        </div>

        <!-- Batch -->
        <div class="card" style="padding:1rem;">
            <h4 style="font-size:0.85rem;font-weight:700;margin-bottom:0.75rem;">Mode Batch</h4>
            <label style="display:flex;align-items:center;gap:0.5rem;font-size:0.8rem;cursor:pointer;">
                <input type="checkbox" id="batchMode" onchange="toggleBatchMode()" style="accent-color:var(--accent-color);">
                Aktifkan Batch (Multi-Halaman)
            </label>
            <p class="text-muted text-xs" style="margin-top:0.5rem;">Upload banyak gambar, gabung jadi satu PDF.</p>
        </div>

        <!-- Simpan -->
        <div class="card" style="padding:1rem;">
            <h4 style="font-size:0.85rem;font-weight:700;margin-bottom:0.75rem;">Simpan ke Database</h4>
            <input type="text" id="docTitle" class="form-control" placeholder="Nama dokumen..." style="font-size:0.8rem;padding:0.5rem 0.75rem;margin-bottom:0.5rem;">
            <select id="docCategory" class="form-control" style="font-size:0.8rem;padding:0.5rem 0.75rem;margin-bottom:0.75rem;">
                <option value="Kwitansi/Struk">Kwitansi/Struk</option>
                <option value="Faktur/Invoice">Faktur/Invoice</option>
                <option value="Kontrak">Kontrak</option>
                <option value="Sertifikat">Sertifikat</option>
                <option value="Pribadi">Pribadi</option>
                <option value="Kerja" selected>Kerja</option>
                <option value="Lainnya">Lainnya</option>
            </select>
            <button class="btn btn-primary btn-glow w-full" style="justify-content:center;" onclick="saveToServer()">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/></svg>
                Simpan
            </button>
        </div>

        <!-- Export -->
        <div class="card" style="padding:1rem;">
            <h4 style="font-size:0.85rem;font-weight:700;margin-bottom:0.75rem;">Export</h4>
            <div class="export-options">
                <button class="export-btn" onclick="exportAs('pdf')">PDF</button>
                <button class="export-btn" onclick="exportAs('jpg')">JPG</button>
                <button class="export-btn" onclick="exportAs('png')">PNG</button>
                <button class="export-btn" onclick="exportAs('txt')">TXT</button>
            </div>
        </div>

    </div>
</div>

<div class="toast" id="toast"></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script>
    if (typeof pdfjsLib !== 'undefined') {
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.2/jspdf.umd.min.js"></script>
<script src="{{ asset('js/scanner.js') }}"></script>

@endsection
