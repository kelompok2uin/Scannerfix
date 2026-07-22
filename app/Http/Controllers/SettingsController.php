<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'app_name' => config('app.name', 'BPKA Scanner'),
            'app_env' => config('app.env', 'local'),
            'app_debug' => config('app.debug', true),
            'app_url' => config('app.url', 'http://localhost'),
            'timezone' => config('app.timezone', 'UTC'),
            'locale' => config('app.locale', 'en'),
            'db_connection' => config('database.default', 'sqlite'),
            'max_upload_size' => '10MB',
            'supported_formats' => 'JPG, PNG, PDF',
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'app_name' => 'nullable|string|max:255',
            'timezone' => 'nullable|string',
            'locale' => 'nullable|string',
        ]);

        return redirect()->route('settings')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
