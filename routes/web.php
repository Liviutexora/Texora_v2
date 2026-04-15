<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\EmployeeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';



Route::get('/reminders', function () {
    return view('reminders');
})->middleware(['auth'])->name('reminders');

Route::get('/appointments', [AppointmentController::class, 'index'])
    ->middleware(['auth'])
    ->name('appointments');

Route::post('/appointments', [AppointmentController::class, 'store'])
    ->middleware(['auth'])
    ->name('appointments.store');

Route::middleware(['auth'])->group(function () {

    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');

});
Route::middleware(['auth'])->group(function () {

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store']);
    Route::get('/services/{id}/edit', [ServiceController::class, 'edit'])->name('services.edit');

Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
Route::delete('/employees/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
Route::get('/employees/{id}', [EmployeeController::class, 'show'])->name('employees.show');
Route::post('/employees/{id}/working-hours', [EmployeeController::class, 'updateWorkingHours'])
    ->name('employees.working-hours.update');
Route::post('/business', [BusinessController::class, 'store'])->name('business.store');

});

Route::view('/reminders', 'placeholder')->name('reminders');
Route::view('/calendar', 'placeholder')->name('calendar.index');

Route::get('/slots', [AppointmentController::class, 'getSlots']);
