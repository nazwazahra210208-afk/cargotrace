<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CargoApiController;

// Authentication
Route::post('/register', [CargoApiController::class, 'register']);
Route::post('/login', [CargoApiController::class, 'login']);
Route::post('/logout', [CargoApiController::class, 'logout']);
Route::get('/user', [CargoApiController::class, 'user']);

// Shipments & Tracking
Route::get('/shipments', [CargoApiController::class, 'shipmentsIndex']);
Route::post('/shipments', [CargoApiController::class, 'shipmentsStore']);
Route::get('/shipments/{id}', [CargoApiController::class, 'shipmentsShow']);
Route::match(['put', 'patch'], '/shipments/{id}', [CargoApiController::class, 'shipmentsUpdate']);
Route::delete('/shipments/{id}', [CargoApiController::class, 'shipmentsDestroy']);
Route::get('/shipments/{id}/tracking', [CargoApiController::class, 'trackingIndex']);
Route::post('/shipments/{id}/tracking', [CargoApiController::class, 'trackingStore']);

// Related Resources (Carriers, Locations, Exceptions)
Route::get('/carriers', [CargoApiController::class, 'carriersIndex']);
Route::post('/carriers', [CargoApiController::class, 'carriersStore']);
Route::get('/locations', [CargoApiController::class, 'locationsIndex']);
Route::post('/locations', [CargoApiController::class, 'locationsStore']);
Route::get('/shipments/{id}/exceptions', [CargoApiController::class, 'exceptionsIndex']);
Route::post('/shipments/{id}/exceptions', [CargoApiController::class, 'exceptionsStore']);
Route::patch('/exceptions/{id}', [CargoApiController::class, 'exceptionsResolve']);