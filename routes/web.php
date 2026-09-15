<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\IrrigationController;

Route::get('/', [IrrigationController::class, 'index'])->name('dashboard');
Route::post('/update-mode', [IrrigationController::class, 'updateMode'])->name('update.mode');
Route::post('/toggle-pompa', [IrrigationController::class, 'togglePompa'])->name('toggle.pompa');
Route::post('/update-jadwal', [IrrigationController::class, 'updateJadwal'])->name('update.jadwal');