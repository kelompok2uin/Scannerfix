<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalDocuments = Document::count();
        $pdfCount = Document::where('file_type', 'pdf')->count();
        $imageCount = Document::whereIn('file_type', ['jpg', 'jpeg', 'png'])->count();
        $ocrSuccess = Document::where('ocr_status', 'completed')->count();
        $ocrRate = $totalDocuments > 0 ? round(($ocrSuccess / $totalDocuments) * 100) : 0;

        $categories = Document::selectRaw('category, count(*) as count')
            ->groupBy('category')
            ->orderByDesc('count')
            ->get();

        $recentDocuments = Document::latest()->take(5)->get();

        return view('dashboard.index', compact(
            'totalDocuments',
            'pdfCount',
            'imageCount',
            'ocrRate',
            'categories',
            'recentDocuments',
        ));
    }
}
