<?php

use App\Http\Controllers\IrrigationController;
use Illuminate\Support\Facades\Route;

// Endpoint untuk dibaca alat IoT
Route::get('/cek-jadwal', [IrrigationController::class, 'getApiData']);