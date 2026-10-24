<?php

use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\Dashboard\FinancialController;
use App\Http\Controllers\Dashboard\OverviewController;
use App\Http\Controllers\Dashboard\RentController;
use App\Http\Controllers\DataTransferController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InspectionController;
use App\Http\Controllers\InspectionMediaController;
use App\Http\Controllers\InspectionTemplateController;
use App\Http\Controllers\LeaseController;
use App\Http\Controllers\LeaseInvoiceController;
use App\Http\Controllers\LeaseRentScheduleController;
use App\Http\Controllers\MaintenanceTicketController;
use App\Http\Controllers\PaymentAttemptController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PropertyAmenityController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\PropertyDocumentsController;
use App\Http\Controllers\PropertyLeasesController;
use App\Http\Controllers\PropertyMediaController;
use App\Http\Controllers\PropertyRateController;
use App\Http\Controllers\PropertyUnitTypeController;
use App\Http\Controllers\PublicListingController;
use App\Http\Controllers\PublicListingMediaController;
use App\Http\Controllers\RecurringExpenseController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Settings\PublicPortalController;
use App\Http\Controllers\SignedPaymentController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantDocumentController;
use App\Http\Controllers\TenantInvitationController;
use App\Http\Controllers\TenantPortal\DashboardController as TenantPortalDashboardController;
use App\Http\Controllers\TenantPortal\LeaseController as TenantPortalLeaseController;
use App\Http\Controllers\TenantPortal\NotificationController as TenantPortalNotificationController;
use App\Http\Controllers\TenantPortal\PaymentController as TenantPortalPaymentController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UnitTypeMediaController;
use App\Http\Controllers\UnitTypeRateController;
use App\Http\Controllers\UnitUtilityController;
use App\Http\Controllers\UserController;
use App\Models\Application;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:public-portal')->group(function () {
    Route::get('/', [PublicListingController::class, 'pageIndex'])->name('public.portal.index');
    Route::redirect('/listings', '/', 308)->name('public.portal.redirect');
    Route::get('listings/media/{media}', [PublicListingMediaController::class, 'show'])
        ->whereNumber('media')
        ->name('public.portal.media');
});

Route::scopeBindings()->prefix('listings')->name('public.portal.')->middleware('throttle:public-portal')->group(function () {
    Route::get('{property:public_slug}', [PublicListingController::class, 'pageShow'])->name('show');
    Route::get('{property:public_slug}/unit-types/{unitType:public_slug}', [PublicListingController::class, 'pageUnitType'])
        ->name('unit-types.show');
});

Route::middleware(['auth', 'verified', 'throttle:auth'])->prefix('portal/applications')->name('applications.')->group(function () {
    Route::get('/', [ApplicationController::class, 'index'])->name('index');
    Route::get('create', [ApplicationController::class, 'create'])->name('create');
    Route::get('{application}', [ApplicationController::class, 'show'])->name('show');
    Route::post('/', [ApplicationController::class, 'store'])->middleware('throttle:auth')->name('store');
    Route::patch('{application}/status', [ApplicationController::class, 'transition'])->name('transition');
    Route::post('{application}/convert', [ApplicationController::class, 'convert'])->name('convert');
});

Route::middleware(['auth', 'verified'])->prefix('applications')->group(function () {
    Route::get('/', fn () => redirect()->route('applications.index', status: 308));
    Route::get('create', fn () => redirect()->route('applications.create', status: 308));
    Route::get('{application}', fn (Application $application) => redirect()->route('applications.show', $application, status: 308));
});

Route::prefix('invitations')->name('users.invitations.')->middleware(['guest', 'throttle:auth'])->group(function () {
    Route::get('{token}', [UserController::class, 'acceptInvitation'])->name('accept');
    Route::post('accept', [UserController::class, 'completeInvitation'])->name('complete');
});

Route::prefix('tenants/invitations')->name('tenants.invitations.')->middleware(['guest', 'throttle:auth'])->group(function () {
    Route::get('{token}', [TenantInvitationController::class, 'acceptInvitation'])->name('accept');
    Route::post('accept', [TenantInvitationController::class, 'completeInvitation'])->name('complete');
});

Route::prefix('pay/invoices/{token}')
    ->where(['token' => '[A-Za-z0-9_-]+'])
    ->middleware(['throttle:public-portal', 'webhook.secure'])
    ->group(function () {
        Route::get('/', [SignedPaymentController::class, 'show'])->name('payments.signed.show');
        Route::post('/', [SignedPaymentController::class, 'pay'])->name('payments.signed.pay');
    });

Route::middleware(['auth', 'verified'])->prefix('portal')->name('portal.')->group(function () {
    Route::get('/', fn () => redirect()->route('portal.dashboard', status: 308));
    Route::get('dashboard', TenantPortalDashboardController::class)->name('dashboard');
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [TenantPortalNotificationController::class, 'index'])->name('index');
        Route::post('{notification}/read', [TenantPortalNotificationController::class, 'markAsRead'])->name('read');
        Route::post('read-all', [TenantPortalNotificationController::class, 'markAllAsRead'])->name('read-all');
    });
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [TenantPortalPaymentController::class, 'index'])->name('index');
        Route::post('/', [TenantPortalPaymentController::class, 'store'])->name('store');
        Route::get('history/invoices', [TenantPortalPaymentController::class, 'invoiceHistory'])->name('history.invoices');
        Route::get('history/payments', [TenantPortalPaymentController::class, 'paymentHistory'])->name('history.payments');
        Route::get('invoices/{invoice}', [TenantPortalPaymentController::class, 'invoice'])->name('invoices.show');
        Route::post('invoices/{invoice}/pay', [TenantPortalPaymentController::class, 'pay'])->name('invoices.pay');
        Route::get('invoices/{invoice}/print', [TenantPortalPaymentController::class, 'print'])->name('invoices.print');
        Route::get('invoices/{invoice}/download', [TenantPortalPaymentController::class, 'download'])->name('invoices.download');
    });

    Route::prefix('leases')->name('lease.')->group(function () {
        Route::get('/', [TenantPortalLeaseController::class, 'index'])->name('index');
        Route::prefix('{lease}')->whereNumber('lease')->group(function () {
            Route::get('/', [TenantPortalLeaseController::class, 'show'])->name('show');
        });
    });

    Route::prefix('maintenance-tickets')->name('maintenance-tickets.')->group(function () {
        Route::get('/', [App\Http\Controllers\TenantPortal\MaintenanceTicketController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\TenantPortal\MaintenanceTicketController::class, 'store'])->name('store');
        Route::get('{ticket}', [App\Http\Controllers\TenantPortal\MaintenanceTicketController::class, 'show'])->name('show');
    });
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('portal/lease', fn () => redirect()->route('portal.lease.index', status: 308));
    Route::get('portal/maintenance', fn () => redirect()->route('portal.maintenance-tickets.index', status: 308));
});

Route::middleware(['auth', 'verified', 'role:owner'])
    ->prefix('public-portal')
    ->name('public-portal.')
    ->group(function () {
        Route::redirect('/', '/public-portal/seo')->name('overview');
        Route::get('seo', [PublicPortalController::class, 'edit'])->name('seo');
        Route::patch('seo', [PublicPortalController::class, 'update'])->name('seo.update');
        Route::post('seo/social-image/{asset}', [PublicPortalController::class, 'updateSocialImage'])
            ->where('asset', 'og-image')
            ->name('seo.social-image.update');
        Route::delete('seo/social-image', [PublicPortalController::class, 'removeSocialImage'])
            ->name('seo.social-image.destroy');
    });

Route::middleware(['auth', 'verified', 'permission:dashboard.view'])->group(function () {
    Route::prefix('dashboard')->group(function () {
        Route::get('/', OverviewController::class)->name('dashboard');
        Route::get('rent', RentController::class)->name('dashboard.rent');
        Route::get('financial', FinancialController::class)
            ->name('dashboard.financial')
            ->middleware('permission:financials.view');
    });
...
