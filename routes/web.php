<?php

use App\Http\Controllers\PdfController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/gatepass', function () {
    return view('gatepass');
})->name('gatepass');

Route::get('/pdf/{id}',[PdfController::class, 'export_pdf'])->name('download_pdf');
