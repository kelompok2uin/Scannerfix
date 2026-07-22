/* ===================== BPKA SCANNER ENGINE ===================== */

let currentFilter = 'original';
let batchMode = false;
let batchImages = [];
let originalImageData = null;
let currentRotation = 0;
let ocrText = '';
let hasImage = false;

const editCanvas = document.getElementById('editCanvas');
const ctx = editCanvas.getContext('2d', { willReadFrequently: true });

/* ===================== INIT ===================== */

document.addEventListener('DOMContentLoaded', () => {
    const fi = document.getElementById('fileUploadInput');
    if (fi) fi.addEventListener('change', e => handleFiles(e.target.files));

    const uz = document.getElementById('uploadZone');
    uz.addEventListener('dragover', e => { e.preventDefault(); uz.classList.add('dragover'); });
    uz.addEventListener('dragleave', () => uz.classList.remove('dragover'));
    uz.addEventListener('drop', e => { e.preventDefault(); uz.classList.remove('dragover'); handleFiles(e.dataTransfer.files); });
});

/* ===================== FILE UPLOAD ===================== */

function handleFiles(files) {
    if (!files || files.length === 0) return;
    for (const file of files) {
        if (file.type === 'application/pdf') {
            handlePDF(file);
        } else {
            handleImage(file);
        }
    }
}

function handleImage(file) {
    const reader = new FileReader();
    reader.onload = e => {
        const img = new Image();
        img.onload = () => {
            editCanvas.width = img.width;
            editCanvas.height = img.height;
            ctx.drawImage(img, 0, 0);
            originalImageData = ctx.getImageData(0, 0, img.width, img.height);
            currentRotation = 0;
            currentFilter = 'original';

            if (batchMode) {
                batchImages.push(editCanvas.toDataURL('image/jpeg', 0.92));
                updateBatchThumbs();
                showToast('Ditambahkan ke batch (' + batchImages.length + ' halaman)', 'success');
            } else {
                document.getElementById('uploadZone').style.display = 'none';
                editCanvas.style.display = 'block';
                document.getElementById('quickActions').style.display = 'flex';
                hasImage = true;
                showToast('File dimuat: ' + file.name, 'success');
            }
            resetSliders();
        };
        img.src = e.target.result;
    };
    reader.readAsDataURL(file);
}

async function handlePDF(file) {
    if (typeof pdfjsLib === 'undefined') {
        showToast('PDF.js belum dimuat, coba lagi', 'error');
        return;
    }
    showToast('Memproses PDF...', 'info');
    const reader = new FileReader();
    reader.onload = async function(e) {
        try {
            const typedarray = new Uint8Array(e.target.result);
            const pdf = await pdfjsLib.getDocument({ data: typedarray }).promise;
            const totalPages = pdf.numPages;

            for (let i = 1; i <= totalPages; i++) {
                const page = await pdf.getPage(i);
                const scale = 2;
                const viewport = page.getViewport({ scale });
                const tempCanvas = document.createElement('canvas');
                tempCanvas.width = viewport.width;
                tempCanvas.height = viewport.height;
                const tempCtx = tempCanvas.getContext('2d');

                await page.render({ canvasContext: tempCtx, viewport }).promise;

                if (batchMode || totalPages > 1) {
                    batchImages.push(tempCanvas.toDataURL('image/jpeg', 0.92));
                } else {
                    editCanvas.width = viewport.width;
                    editCanvas.height = viewport.height;
                    ctx.drawImage(tempCanvas, 0, 0);
                    originalImageData = ctx.getImageData(0, 0, viewport.width, viewport.height);
                    currentRotation = 0;
                    currentFilter = 'original';
                    document.getElementById('uploadZone').style.display = 'none';
                    editCanvas.style.display = 'block';
                    document.getElementById('quickActions').style.display = 'flex';
                    hasImage = true;
                }
            }

            if (batchMode || totalPages > 1) {
                updateBatchThumbs();
                document.getElementById('batchMode').checked = true;
                batchMode = true;
                document.getElementById('batchSection').style.display = 'block';
                showToast('PDF: ' + totalPages + ' halaman ditambahkan', 'success');
            } else {
                showToast('PDF dimuat: ' + file.name, 'success');
            }
            resetSliders();
        } catch (err) {
            showToast('Gagal membaca PDF: ' + err.message, 'error');
        }
    };
    reader.readAsArrayBuffer(file);
}

/* ===================== CLEAR ===================== */

function clearCanvas() {
    ctx.clearRect(0, 0, editCanvas.width, editCanvas.height);
    editCanvas.style.display = 'none';
    editCanvas.width = 1;
    editCanvas.height = 1;
    document.getElementById('uploadZone').style.display = 'flex';
    document.getElementById('quickActions').style.display = 'none';
    originalImageData = null;
    hasImage = false;
    ocrText = '';
    currentFilter = 'original';
    currentRotation = 0;
    resetSliders();
    resetFilterButtons();
    document.getElementById('ocrResult').innerHTML = '<span class="text-muted">Hasil OCR muncul di sini...</span>';
    document.getElementById('ocrProgress').style.display = 'none';
    document.getElementById('translateResult').style.display = 'none';
    showToast('Dihapus', 'info');
}

/* ===================== FILTERS ===================== */

function resetSliders() {
    ['brightness', 'contrast', 'saturation'].forEach(id => {
        document.getElementById(id).value = 0;
        document.getElementById(id + 'Val').textContent = '0';
    });
}

function resetFilterButtons() {
    ['fOriginal', 'fBw', 'fGrayscale', 'fMagic', 'fContrast', 'fSharp'].forEach(id => {
        document.getElementById(id).classList.remove('active');
    });
    document.getElementById('fOriginal').classList.add('active');
}

function applyFilter(name) {
    currentFilter = name;
    resetFilterButtons();
    const map = { original: 'fOriginal', bw: 'fBw', grayscale: 'fGrayscale', magic: 'fMagic', contrast: 'fContrast', sharp: 'fSharp' };
    if (map[name]) document.getElementById(map[name]).classList.add('active');
    updateImage();
}

function updateImage() {
    if (!originalImageData) return;

    const brightness = parseInt(document.getElementById('brightness').value);
    const contrast = parseInt(document.getElementById('contrast').value);
    const saturation = parseInt(document.getElementById('saturation').value);
    document.getElementById('brightnessVal').textContent = brightness;
    document.getElementById('contrastVal').textContent = contrast;
    document.getElementById('saturationVal').textContent = saturation;

    const w = originalImageData.width;
    const h = originalImageData.height;
    const src = originalImageData.data;
    const out = new Uint8ClampedArray(src);

    for (let i = 0; i < src.length; i += 4) {
        let r = src[i], g = src[i + 1], b = src[i + 2];

        r += brightness * 2.55;
        g += brightness * 2.55;
        b += brightness * 2.55;

        const f = (259 * (contrast + 255)) / (255 * (259 - contrast));
        r = f * (r - 128) + 128;
        g = f * (g - 128) + 128;
        b = f * (b - 128) + 128;

        const gray = 0.2126 * r + 0.7152 * g + 0.0722 * b;
        r = gray + (1 + saturation / 100) * (r - gray);
        g = gray + (1 + saturation / 100) * (g - gray);
        b = gray + (1 + saturation / 100) * (b - gray);

        switch (currentFilter) {
            case 'bw':
                const v = (r * 0.299 + g * 0.587 + b * 0.114) > 128 ? 255 : 0;
                r = g = b = v;
                break;
            case 'grayscale':
                r = g = b = r * 0.299 + g * 0.587 + b * 0.114;
                break;
            case 'magic':
                r = Math.min(255, r * 1.1 + 10);
                g = Math.min(255, g * 1.05);
                b = Math.min(255, b * 0.95 + 5);
                break;
            case 'contrast':
                const cf = 1.5;
                r = cf * (r - 128) + 128;
                g = cf * (g - 128) + 128;
                b = cf * (b - 128) + 128;
                break;
        }

        out[i] = Math.max(0, Math.min(255, r));
        out[i + 1] = Math.max(0, Math.min(255, g));
        out[i + 2] = Math.max(0, Math.min(255, b));
        out[i + 3] = src[i + 3];
    }

    const tempCanvas = document.createElement('canvas');
    tempCanvas.width = w;
    tempCanvas.height = h;
    tempCanvas.getContext('2d').putImageData(new ImageData(out, w, h), 0, 0);

    if (currentRotation !== 0) {
        const isV = Math.abs(currentRotation) === 90 || Math.abs(currentRotation) === 270;
        editCanvas.width = isV ? h : w;
        editCanvas.height = isV ? w : h;
        ctx.save();
        ctx.translate(editCanvas.width / 2, editCanvas.height / 2);
        ctx.rotate((currentRotation * Math.PI) / 180);
        ctx.drawImage(tempCanvas, -w / 2, -h / 2);
        ctx.restore();
    } else {
        editCanvas.width = w;
        editCanvas.height = h;
        ctx.putImageData(new ImageData(out, w, h), 0, 0);
    }
}

function rotateImage(deg) {
    if (!originalImageData) return;
    currentRotation = (currentRotation + deg) % 360;

    const tmp = document.createElement('canvas');
    tmp.width = originalImageData.width;
    tmp.height = originalImageData.height;
    tmp.getContext('2d').putImageData(originalImageData, 0, 0);

    const isV = Math.abs(currentRotation) === 90 || Math.abs(currentRotation) === 270;
    const nw = isV ? originalImageData.height : originalImageData.width;
    const nh = isV ? originalImageData.width : originalImageData.height;

    editCanvas.width = nw;
    editCanvas.height = nh;
    ctx.save();
    ctx.translate(nw / 2, nh / 2);
    ctx.rotate((currentRotation * Math.PI) / 180);
    ctx.drawImage(tmp, -originalImageData.width / 2, -originalImageData.height / 2);
    ctx.restore();

    originalImageData = ctx.getImageData(0, 0, nw, nh);
    showToast('Diputar ' + deg + '\u00B0', 'info');
}

/* ===================== AUTO CROP ===================== */

function autoCrop() {
    if (!originalImageData) return;
    const src = originalImageData.data;
    const w = originalImageData.width;
    const h = originalImageData.height;
    let top = h, bottom = 0, left = w, right = 0;

    for (let y = 0; y < h; y++) {
        for (let x = 0; x < w; x++) {
            const i = (y * w + x) * 4;
            if ((src[i] + src[i + 1] + src[i + 2]) / 3 < 230) {
                if (y < top) top = y;
                if (y > bottom) bottom = y;
                if (x < left) left = x;
                if (x > right) right = x;
            }
        }
    }

    const pad = 15;
    top = Math.max(0, top - pad);
    left = Math.max(0, left - pad);
    right = Math.min(w - 1, right + pad);
    bottom = Math.min(h - 1, bottom + pad);

    const cw = right - left, ch = bottom - top;
    if (cw > 10 && ch > 10) {
        const cropped = ctx.getImageData(left, top, cw, ch);
        editCanvas.width = cw;
        editCanvas.height = ch;
        ctx.putImageData(cropped, 0, 0);
        originalImageData = ctx.getImageData(0, 0, cw, ch);
        showToast('Auto crop diterapkan', 'success');
    } else {
        showToast('Tidak ada tepi dokumen terdeteksi', 'error');
    }
}

function perspectiveFix() {
    if (!originalImageData) return;
    const d = originalImageData.data;
    for (let i = 0; i < d.length; i += 4) {
        const avg = (d[i] + d[i + 1] + d[i + 2]) / 3;
        const v = avg > 180 ? 255 : avg < 60 ? 0 : avg;
        d[i] = v; d[i + 1] = v; d[i + 2] = v;
    }
    ctx.putImageData(originalImageData, 0, 0);
    originalImageData = ctx.getImageData(0, 0, editCanvas.width, editCanvas.height);
    showToast('Koreksi sudut diterapkan', 'success');
}

/* ===================== BATCH ===================== */

function toggleBatchMode() {
    batchMode = document.getElementById('batchMode').checked;
    document.getElementById('batchSection').style.display = batchMode ? 'block' : 'none';
    if (!batchMode) { batchImages = []; updateBatchThumbs(); }
}

function updateBatchThumbs() {
    const c = document.getElementById('batchThumbs');
    c.innerHTML = '';
    batchImages.forEach((img, i) => {
        const d = document.createElement('div');
        d.className = 'batch-thumb' + (i === batchImages.length - 1 ? ' active' : '');
        d.innerHTML = '<img src="' + img + '"><button class="remove" onclick="removeBatchPage(' + i + ',event)">&times;</button>';
        d.onclick = () => loadBatchPage(i);
        c.appendChild(d);
    });
    document.getElementById('batchCount').textContent = batchImages.length + ' halaman';
}

function loadBatchPage(i) {
    const img = new Image();
    img.onload = () => {
        editCanvas.width = img.width;
        editCanvas.height = img.height;
        ctx.drawImage(img, 0, 0);
        originalImageData = ctx.getImageData(0, 0, img.width, img.height);
        document.querySelectorAll('.batch-thumb').forEach((t, j) => t.classList.toggle('active', j === i));
    };
    img.src = batchImages[i];
}

function removeBatchPage(i, e) {
    e.stopPropagation();
    batchImages.splice(i, 1);
    updateBatchThumbs();
}

/* ===================== OCR ===================== */

async function runOCR() {
    if (!originalImageData) { showToast('Upload gambar dulu', 'error'); return; }
    const prog = document.getElementById('ocrProgress');
    const fill = document.getElementById('ocrProgressFill');
    const res = document.getElementById('ocrResult');
    prog.style.display = 'block';
    fill.style.width = '0%';
    res.innerHTML = '<span class="text-muted">Memproses OCR...</span>';

    try {
        const worker = await Tesseract.createWorker('ind+eng', 1, {
            logger: m => { if (m.status === 'recognizing text') fill.style.width = Math.round(m.progress * 100) + '%'; }
        });
        const { data } = await worker.recognize(editCanvas.toDataURL('image/png'));
        ocrText = data.text;
        res.textContent = data.text || 'Teks tidak terdeteksi.';
        fill.style.width = '100%';
        await worker.terminate();
        showToast('OCR selesai!', 'success');
    } catch (err) {
        res.textContent = 'Gagal: ' + err.message;
        showToast('OCR gagal', 'error');
    }
}

function copyOCRText() {
    if (!ocrText) return;
    navigator.clipboard.writeText(ocrText).then(() => showToast('Teks disalin', 'success'));
}

async function translateOCRText() {
    if (!ocrText) return;
    const box = document.getElementById('translateResult');
    box.style.display = 'block';
    box.textContent = 'Menerjemahkan...';
    try {
        const r = await fetch('https://api.mymemory.translated.net/get?q=' + encodeURIComponent(ocrText.substring(0, 500)) + '&langpair=id|en');
        const d = await r.json();
        box.textContent = d.responseData?.translatedText || 'Gagal';
    } catch { box.textContent = 'Gagal menerjemahkan.'; }
}

/* ===================== EXPORT ===================== */

function exportAs(format) {
    if (!originalImageData) { showToast('Tidak ada gambar', 'error'); return; }
    if (format === 'pdf') { exportPDF(); return; }
    if (format === 'txt') {
        if (!ocrText) { showToast('Jalankan OCR dulu', 'error'); return; }
        downloadBlob(new Blob([ocrText], { type: 'text/plain' }), 'dokumen.txt');
        return;
    }
    const a = document.createElement('a');
    a.href = editCanvas.toDataURL(format === 'jpg' ? 'image/jpeg' : 'image/png', 0.95);
    a.download = 'dokumen.' + format;
    a.click();
    showToast('Diekspor sebagai ' + format.toUpperCase(), 'success');
}

function exportPDF() {
    const imgs = batchMode && batchImages.length > 0 ? batchImages : [editCanvas.toDataURL('image/jpeg', 0.92)];
    const { jsPDF } = window.jspdf;
    const pdf = new jsPDF('p', 'mm', 'a4');
    imgs.forEach((d, i) => {
        if (i > 0) pdf.addPage();
        pdf.addImage(d, 'JPEG', 0, 0, pdf.internal.pageSize.getWidth(), pdf.internal.pageSize.getHeight());
    });
    pdf.save('dokumen.pdf');
    showToast('PDF diekspor (' + imgs.length + ' halaman)', 'success');
}

/* ===================== QUICK SAVE ===================== */

async function quickSave() {
    if (!originalImageData) { showToast('Tidak ada gambar', 'error'); return; }
    const imgs = batchMode && batchImages.length > 0 ? batchImages : [editCanvas.toDataURL('image/jpeg', 0.92)];
    const title = 'Dokumen_' + new Date().toISOString().slice(0, 10) + '_' + Date.now();

    try {
        const r = await fetch('/scanner/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ title, images: imgs, category: 'Lainnya', ocr_text: ocrText || null })
        });
        const d = await r.json();
        if (d.success) {
            showToast('Tersimpan cepat!', 'success');
            setTimeout(() => window.location.href = d.redirect, 800);
        }
    } catch (err) { showToast('Gagal: ' + err.message, 'error'); }
}

/* ===================== SAVE WITH FORM ===================== */

async function saveToServer() {
    if (!originalImageData) { showToast('Upload gambar dulu', 'error'); return; }
    const title = document.getElementById('docTitle').value.trim();
    if (!title) { showToast('Masukkan nama dokumen', 'error'); return; }

    const imgs = batchMode && batchImages.length > 0 ? batchImages : [editCanvas.toDataURL('image/jpeg', 0.92)];
    const cat = document.getElementById('docCategory').value;

    try {
        const r = await fetch('/scanner/save', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            body: JSON.stringify({ title, images: imgs, category: cat, ocr_text: ocrText || null })
        });
        const d = await r.json();
        if (d.success) {
            showToast('Tersimpan!', 'success');
            setTimeout(() => window.location.href = d.redirect, 800);
        } else {
            showToast('Gagal menyimpan', 'error');
        }
    } catch (err) { showToast('Error: ' + err.message, 'error'); }
}

/* ===================== HELPERS ===================== */

function downloadBlob(blob, name) {
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = name;
    a.click();
    showToast('File diunduh', 'success');
}

function showToast(msg, type) {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + (type || 'info') + ' show';
    clearTimeout(t._tid);
    t._tid = setTimeout(() => t.classList.remove('show'), 3000);
}
