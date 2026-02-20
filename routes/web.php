<?php

use App\Http\Controllers\StudentPdfDownloadController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/students/{student}/pdf', StudentPdfDownloadController::class)
        ->name('students.pdf.download');
});
