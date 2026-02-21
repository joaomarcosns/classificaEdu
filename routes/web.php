<?php

use App\Http\Controllers\StudentPdfDownloadController;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    $loginUrl = Filament::getLoginUrl();

    return $loginUrl ? redirect()->to($loginUrl) : abort(404);
})->name('login');

Route::middleware('auth')->group(function () {
    Route::get('/students/{student}/pdf', StudentPdfDownloadController::class)
        ->name('students.pdf.download');
});
