<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ScannerController extends Controller
{
    public function index()
    {
        return view('scanner.index');
    }

    public function processScan(Request $request)
    {
        $request->validate([
            'document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'title' => 'nullable|string|max:255',
        ]);

        $file = $request->file('document');
        $title = $request->input('title', pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $ext = strtolower($file->getClientOriginalExtension());

        $dir = storage_path('app/public/documents');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = Str::slug($title) . '_' . time() . '.' . $ext;

        if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getPathname());
            $image->grayscale()->contrast(20);
            $image->toJpeg(90)->save($dir . '/' . $filename);
            $fileType = 'jpg';
        } else {
            $file->move($dir, $filename);
            $fileType = 'pdf';
        }

        $doc = Document::create([
            'user_id' => 1,
            'title' => $title,
            'file_path' => 'documents/' . $filename,
            'file_type' => $fileType,
            'file_size' => $file->getSize(),
            'category' => 'Lainnya',
            'ocr_status' => 'pending',
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Dokumen berhasil diunggah.',
            'data' => $doc,
        ]);
    }

    public function save(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'images' => 'required|array',
            'category' => 'nullable|string',
        ]);

        $title = $request->input('title');
        $category = $request->input('category', 'Lainnya');
        $ocrText = $request->input('ocr_text');
        $images = $request->input('images');

        $dir = storage_path('app/public/documents');
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = Str::slug($title) . '_' . time() . '.jpg';
        $firstImage = $images[0];
        $data = explode(',', $firstImage);
        $binary = base64_decode($data[1] ?? $data[0]);
        file_put_contents($dir . '/' . $filename, $binary);

        $doc = Document::create([
            'user_id' => 1,
            'title' => $title,
            'file_path' => 'documents/' . $filename,
            'file_type' => 'jpg',
            'file_size' => strlen($binary),
            'category' => $category,
            'ocr_status' => $ocrText ? 'completed' : 'pending',
            'ocr_text' => $ocrText,
        ]);

        return response()->json([
            'success' => true,
            'document' => $doc,
            'redirect' => route('documents.show', $doc),
        ]);
    }
}
