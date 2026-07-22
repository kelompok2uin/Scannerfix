<?php

namespace Database\Seeders;

use App\Models\Document;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::create([
            'name' => 'Admin BPKA',
            'email' => 'admin@bpka.go.id',
            'password' => bcrypt('password'),
        ]);

        $operator = User::create([
            'name' => 'Operator Scanner',
            'email' => 'operator@bpka.go.id',
            'password' => bcrypt('password'),
        ]);

        User::factory(3)->create();

        $categories = ['Kwitansi/Struk', 'Faktur/Invoice', 'Kontrak', 'Sertifikat', 'Pribadi', 'Kerja', 'Lainnya'];
        $statuses = ['pending', 'processing', 'completed', 'completed', 'completed'];

        $sampleDocs = [
            ['title' => 'Laporan Keuangan Q2 2026', 'file_type' => 'pdf', 'file_size' => 2048000, 'ocr_text' => 'LAPORAN KEUANGAN KUARTAL II TAHUN 2026\n\nPendapatan: Rp 1.250.000.000\nBelanja: Rp 980.000.000\nSurplus: Rp 270.000.000'],
            ['title' => 'Surat Masuk - Disdikbud', 'file_type' => 'jpg', 'file_size' => 1536000, 'ocr_text' => 'SURAT MASUK\nNomor: 005/SM/VII/2026\nDari: Dinas Pendidikan dan Kebudayaan\nPerihal: Pengajuan Anggaran Pelatihan'],
            ['title' => 'Kontrak Kerja PT Maju', 'file_type' => 'pdf', 'file_size' => 3072000, 'ocr_text' => null],
            ['title' => 'Kwitansi Pembayaran Server', 'file_type' => 'jpg', 'file_size' => 890000, 'ocr_text' => 'KWITANSI\nNo: KW-2026-0715\nPembayaran: Sewa Server Bulanan\nJumlah: Rp 15.000.000'],
            ['title' => 'Sertifikat Tanah Blok A', 'file_type' => 'pdf', 'file_size' => 4096000, 'ocr_text' => 'SERTIFIKAT HAK MILIK\nNomor: 00123/Blok A/2026\nPemilik: Pemerintah Kab. BPKA'],
            ['title' => 'Invoice Supplier ATK', 'file_type' => 'pdf', 'file_size' => 512000, 'ocr_text' => 'INVOICE\nNo: INV-2026-0892\nItem: ATK dan Perlengkapan Kantor\nTotal: Rp 2.750.000'],
            ['title' => 'Foto Gedung Kantor', 'file_type' => 'jpeg', 'file_size' => 2560000, 'ocr_text' => null],
            ['title' => 'SPJ Triwulan I', 'file_type' => 'pdf', 'file_size' => 1800000, 'ocr_text' => 'SURAT PERTANGGUNGJAWABAN\nTriwulan I Tahun 2026\nRealisasi Anggaran: 23.5%'],
            ['title' => 'Surat Edaran Gubernur', 'file_type' => 'pdf', 'file_size' => 640000, 'ocr_text' => 'SURAT EDARAN\nNomor: SE-2026/045\nTentang: Efisiensi Anggaran Daerah'],
            ['title' => 'Bukti Transfer Kas', 'file_type' => 'jpg', 'file_size' => 1024000, 'ocr_text' => 'BUKTI TRANSFER\nBank: BPD\nDari: Kas Daerah\nKe: Rekening Operasional\nNominal: Rp 50.000.000'],
            ['title' => 'Daftar Hadir Rapat', 'file_type' => 'pdf', 'file_size' => 320000, 'ocr_text' => null],
            ['title' => 'Dokumen Proyek Infrastruktur', 'file_type' => 'pdf', 'file_size' => 5120000, 'ocr_text' => 'DOKUMEN PELAKSANAAN\nProyek: Peningkatan Jalan Kabupaten\nAnggaran: Rp 12.500.000.000'],
            ['title' => 'Pajak Kendaraan Dinas', 'file_type' => 'jpg', 'file_size' => 768000, 'ocr_text' => 'BUKTI PEMBAYARAN PAJAK\nKendaraan: Toyota Innova\nNo Pol: DA 1234 XX\nPajak: Rp 3.500.000'],
            ['title' => 'Notulen Rapat Bulanan', 'file_type' => 'pdf', 'file_size' => 256000, 'ocr_text' => 'NOTULEN RAPAT\nTanggal: 1 Juli 2026\nPembahasan: Evaluasi Kinerja Bulanan'],
            ['title' => 'Absensi Pegawai Juli', 'file_type' => 'pdf', 'file_size' => 180000, 'ocr_text' => null],
        ];

        foreach ($sampleDocs as $i => $doc) {
            Document::create([
                'user_id' => $i % 2 === 0 ? $admin->id : $operator->id,
                'title' => $doc['title'],
                'file_path' => 'documents/' . Str::slug($doc['title']) . '.' . $doc['file_type'],
                'file_type' => $doc['file_type'],
                'file_size' => $doc['file_size'],
                'category' => $categories[array_rand($categories)],
                'ocr_status' => $statuses[array_rand($statuses)],
                'ocr_text' => $doc['ocr_text'],
            ]);
        }
    }
}
