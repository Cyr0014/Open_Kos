<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/bank-statements', [App\Http\Controllers\BankStatementImportController::class, 'index'])->name('bank-statements.index');
    Route::get('/bank-statements/create', [App\Http\Controllers\BankStatementImportController::class, 'create'])->name('bank-statements.create');
    Route::post('/bank-statements/upload', [App\Http\Controllers\BankStatementImportController::class, 'store'])->name('bank-statements.store');

    Route::get('/bank-statements/reconciliation', [App\Http\Controllers\BankStatementReconciliationController::class, 'index'])->name('bank-statements.reconciliation.index');
    Route::get('/bank-statements/reconciliation/{import}', [App\Http\Controllers\BankStatementReconciliationController::class, 'review'])->name('bank-statements.reconciliation.review');
    Route::post('/bank-statements/reconciliation/{import}/approve', [App\Http\Controllers\BankStatementReconciliationController::class, 'approve'])->name('bank-statements.reconciliation.approve');
    Route::get('/bank-statements/reconciliation/{import}/export', [App\Http\Controllers\BankStatementReconciliationController::class, 'export'])->name('bank-statements.reconciliation.export');
});
