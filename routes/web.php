<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Models\Service;
use App\Models\Reservation;
use Illuminate\Support\Facades\Route;

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
    Route::resource('services', ServiceController::class);
    Route::resource('reservations', ReservationController::class)->only(['store', 'destroy']);
    Route::get('/worker/dashboard', function () {
        if (auth()->user()->role !== 'worker') abort(403);
        return view('worker.dashboard');
    })->name('worker.dashboard');
    Route::get('/worker/reservations-data', function () {
        if (auth()->user()->role !== 'worker') return response()->json(['message' => 'Unauthorized'], 403);
        $worker = auth()->user();
        $reservations = $worker->reservationsAsWorker()->with('service', 'customer')->get();
        $colors = ['event-blue', 'event-green', 'event-purple', 'event-orange'];
        $events = $reservations->map(function ($reservation, $index) use ($colors) {
            return ['title' => $reservation->service->name . ' - ' . $reservation->customer->name, 'start' => $reservation->start_time, 'end' => $reservation->end_time, 'id' => $reservation->id, 'className' => $colors[$index % count($colors)], 'extendedProps' => ['serviceName' => $reservation->service->name, 'customerName' => $reservation->customer->name, 'price' => number_format($reservation->service->price, 2, ',', '.') . ' €', 'duration' => $reservation->service->duration . ' minuti',]];
        });
        return response()->json($events);
    })->name('worker.reservations.data');
    Route::get('/my-reservations', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');
    Route::get('/services/{service}/reservations', function (Service $service) {
        $reservations = $service->reservations()->where('start_time', '>=', now())->get();
        $events = $reservations->map(function ($reservation) {
            return ['title' => 'Occupato', 'start' => $reservation->start_time, 'end' => $reservation->end_time, 'backgroundColor' => '#d1d5db', 'borderColor' => '#d1d5db',];
        });
        return response()->json($events);
    })->name('services.reservations');
});

Route::middleware(['auth', 'is.superadmin'])->group(function () {
    Route::get('/superadmin/dashboard', [SuperAdminDashboardController::class, 'index'])->name('superadmin.dashboard');
    Route::get('/superadmin/calendar', [SuperAdminDashboardController::class, 'calendar'])->name('superadmin.calendar');
    Route::get('/superadmin/reservations-data', function () {
        $reservations = Reservation::with('service.worker', 'customer')->get();
        $colors = ['event-blue', 'event-green', 'event-purple', 'event-orange'];
        $events = $reservations->map(function ($reservation, $index) use ($colors) {
            return ['id' => $reservation->id, 'title' => $reservation->service->name . ' - ' . $reservation->customer->name, 'start' => $reservation->start_time, 'end' => $reservation->end_time, 'className' => $colors[$index % count($colors)], 'extendedProps' => ['serviceName' => $reservation->service->name, 'customerName' => $reservation->customer->name, 'workerName' => $reservation->service->worker->name,]];
        });
        return response()->json($events);
    })->name('superadmin.reservations.data');
});

require __DIR__ . '/auth.php';
