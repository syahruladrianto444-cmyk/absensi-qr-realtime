<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes — Absensi Mahasiswa
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/absen/{token}', [AbsensiController::class, 'showForm'])->name('absensi.form');
Route::post('/validate-location', [AbsensiController::class, 'validateLocation'])->name('absensi.validate');
Route::post('/submit-attendance', [AbsensiController::class, 'submitAttendance'])->name('absensi.submit');
Route::get('/absensi-success', fn() => view('absensi.success'))->name('absensi.success');
Route::get('/absensi-expired', fn() => view('absensi.expired'))->name('absensi.expired');

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes (Protected)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Events
    Route::get('/events', [DashboardController::class, 'events'])->name('events.index');
    Route::get('/events/create', [DashboardController::class, 'createEvent'])->name('events.create');
    Route::post('/events', [DashboardController::class, 'storeEvent'])->name('events.store');
    Route::get('/events/{event}', [DashboardController::class, 'showEvent'])->name('events.show');
    Route::post('/events/{event}/toggle', [DashboardController::class, 'toggleEvent'])->name('events.toggle');

    // QR Code
    Route::get('/events/{event}/qr/refresh', [DashboardController::class, 'refreshQr'])->name('events.qr.refresh');

    // Attendance
    Route::get('/events/{event}/attendance', [DashboardController::class, 'attendanceLogs'])->name('attendance.index');
    Route::get('/events/{event}/export', [DashboardController::class, 'exportCsv'])->name('attendance.export');
});
