<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $query = Document::query();

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('ocr_status', $request->status);
        }

        $documents = $query->latest()->paginate(12);

        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'files' => 'required|array',
            'files.*' => 'file|max:10240|mimes:pdf,jpg,jpeg,png',
        ]);

        foreach ($request->file('files') as $file) {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($originalName) . '_' . time() . '.' . $extension;
            $path = $file->storeAs('documents', $filename, 'public');

            $type = strtolower($extension);
            $category = match (true) {
                in_array($type, ['pdf']) => 'Kwitansi/Struk',
                in_array($type, ['jpg', 'jpeg', 'png']) => 'Kerja',
                default => 'Lainnya',
            };

            Document::create([
                'user_id' => 1,
                'title' => $originalName,
                'file_path' => $path,
                'file_type' => $type,
                'file_size' => $file->getSize(),
                'category' => $category,
                'ocr_status' => 'pending',
            ]);
        }

        return redirect()->back()->with('success', 'Dokumen berhasil diupload.');
    }

    public function show(Document $document)
    {
        return view('documents.show', compact('document'));
    }

    public function download(Document $document): StreamedResponse
    {
        $path = storage_path('app/public/' . $document->file_path);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        $mimeType = mime_content_type($path);
        $filename = $document->title . '.' . $document->file_type;

        return response()->streamDownload(function () use ($path) {
            readfile($path);
        }, $filename, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function destroy(Document $document)
    {
        $path = storage_path('app/public/' . $document->file_path);
        if (file_exists($path)) {
            unlink($path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }
}
