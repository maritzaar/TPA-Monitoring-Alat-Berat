<?php

use App\Http\Controllers\ImportController;
use App\Http\Controllers\MonitoringController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('monitoring.index');
});

// Language Switcher Route
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['id', 'en'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login']);
    Route::get('register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('register', [RegisterController::class, 'register']);
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    // Profile & Help
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('bantuan', function () {
        return view('help.index');
    })->name('bantuan.index');

    Route::get('monitoring/chart', [MonitoringController::class, 'chart'])->name('monitoring.chart');
    Route::get('monitoring/detail/{idAset}', [MonitoringController::class, 'detail'])->name('monitoring.detail');
    Route::get('monitoring/export', [MonitoringController::class, 'export'])->name('monitoring.export');
    Route::resource('monitoring', MonitoringController::class);

    Route::get('import', [ImportController::class, 'index'])->name('import.index');
    Route::post('import', [ImportController::class, 'import'])->name('import.upload');
    Route::get('import/clear', [ImportController::class, 'clearData'])->name('import.clear');
    Route::delete('import/delete/{id}', [ImportController::class, 'deleteLog'])->name('import.delete-log');
});