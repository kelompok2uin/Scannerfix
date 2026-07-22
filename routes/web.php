<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ScannerController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/scanner', [ScannerController::class, 'index'])->name('scanner');
Route::post('/process-scan', [ScannerController::class, 'processScan'])->name('process-scan');
Route::post('/scanner/save', [ScannerController::class, 'save'])->name('scanner.save');

Route::get('/documents', [DocumentController::class, 'index'])->name('documents.index');
Route::post('/documents', [DocumentController::class, 'store'])->name('documents.store');
Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('documents.show');
Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('documents.destroy');

Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
Route::put('/settings', [SettingsController::class, 'update'])->name('settings.update');
