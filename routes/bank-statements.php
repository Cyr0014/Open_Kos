<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('bank-statements')->name('bank-statements.')->group(function () {
    Route::get('/', [App\Http\Controllers\BankStatementImportController::class, 'index'])->name('index');
    Route::get('/create', [App\Http\Controllers\BankStatementImportController::class, 'create'])->name('create');
    Route::post('/upload', [App\Http\Controllers\BankStatementImportController::class, 'store'])->name('store');
});
