<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\ShipmentController;
use App\Http\Controllers\ShipmentExceptionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\CarrierController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\UserController;

// ==========================================
// RUTE PUBLIK & BAHASA (Bisa diakses siapa saja)
// ==========================================

// Rute Ganti Bahasa
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        Session::put('locale', $locale);
    }
    return redirect()->back();
})->name('admin.lang');

// Halaman utama (root) menampilkan form login (GET)
Route::get('/', [AuthController::class, 'showLogin'])->name('login');

// Menangani POST langsung dari form root (/)
Route::post('/', [AuthController::class, 'login']);

// Rute GET dan POST cadangan untuk login & logout
Route::get('/login', [AuthController::class, 'showLogin']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

// Halaman Beranda Publik untuk pelacakan resi
Route::get('/track', function () {
    return view('welcome');
});

// Route publik untuk pencarian resi
Route::post('/track/search', [ShipmentController::class, 'search']);

// Route untuk fitur lacak resi pengiriman
Route::get('/track/{shipmentNumber}', [ShipmentController::class, 'track']);


// ==========================================
// RUTE YANG DILINDUNGI (Semua User Login: Operator & Customer)
// ==========================================
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
});


// ==========================================
// RUTE YANG DILINDUNGI (Khusus Operator)
// ==========================================
Route::middleware(['auth', 'role:operator'])->group(function () {
    Route::get('/shipments', [ShipmentController::class, 'index']);
    Route::get('/shipments/create', [ShipmentController::class, 'create']);
    Route::post('/shipments', [ShipmentController::class, 'store']);
    Route::post('/shipments/{id}/status', [ShipmentController::class, 'operatorUpdateStatus'])->name('operator.shipments.status');
    Route::post('/shipments/{id}/tracking-events', [ShipmentController::class, 'storeTrackingEvent'])->name('operator.shipments.events');
    Route::post('/shipments/{id}/exceptions', [ShipmentExceptionController::class, 'store']);
});


// ==========================================
// RUTE KHUSUS ADMINISTRATOR
// ==========================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Manajemen Carrier, Lokasi, dan User
    Route::resource('carriers', CarrierController::class);
    Route::resource('locations', LocationController::class);
    Route::resource('users', UserController::class);

    // Pemetaan Manual Shipment Khusus Admin agar aman dari error 403 / salah arah
    Route::get('/shipments', [ShipmentController::class, 'adminIndex'])->name('shipments.index');
    Route::get('/shipments/create', [ShipmentController::class, 'adminCreate'])->name('shipments.create');
    Route::post('/shipments', [ShipmentController::class, 'adminStore'])->name('shipments.store');
    Route::get('/shipments/{id}', [ShipmentController::class, 'show'])->name('shipments.show');
    Route::get('/shipments/{id}/edit', [ShipmentController::class, 'edit'])->name('shipments.edit');
    Route::put('/shipments/{id}', [ShipmentController::class, 'update'])->name('shipments.update');
    Route::delete('/shipments/{id}', [ShipmentController::class, 'destroy'])->name('shipments.destroy');
});