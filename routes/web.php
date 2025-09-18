<?php

use App\Http\Controllers\CustomerDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SuperAdminDashboardController;
use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rotte Pubbliche
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Rotte Autenticate (tutti i ruoli)
|--------------------------------------------------------------------------
|
| Questo gruppo contiene tutte le rotte che richiedono il login.
| Al suo interno, abbiamo altri sottogruppi specifici per ruolo.
|
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // --- Dashboard Generica ---
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- Gestione Profilo ---
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // --- Risorse Principali ---
    Route::resource('services', ServiceController::class);
    Route::resource('reservations', ReservationController::class)->only(['store', 'destroy']);
    Route::get('/services/{service}/reservations', function (Service $service) {
        $reservations = $service->reservations()->where('start_time', '>=', now())->get();
        $events = $reservations->map(function ($reservation) {
            return ['title' => 'Occupato', 'start' => $reservation->start_time, 'end' => $reservation->end_time, 'backgroundColor' => '#d1d5db', 'borderColor' => '#d1d5db'];
        });
        return response()->json($events);
    })->name('services.reservations');

    // --- Rotte per il Ruolo: Customer ---
    Route::get('/my-reservations', [CustomerDashboardController::class, 'index'])->name('customer.dashboard');

    // --- Rotte per il Ruolo: Worker ---
    Route::prefix('worker')->name('worker.')->group(function () {
        Route::get('/dashboard', function () {
            if (auth()->user()->role !== 'worker') abort(403);
            return view('worker.dashboard');
        })->name('dashboard');

        Route::get('/reservations-data', function () {
            if (auth()->user()->role !== 'worker') return response()->json(['message' => 'Unauthorized'], 403);
            $worker = auth()->user();
            $reservations = $worker->reservationsAsWorker()->with('service', 'customer')->get();
            $colors = ['event-blue', 'event-green', 'event-purple', 'event-orange'];
            $events = $reservations->map(function ($reservation, $index) use ($colors) {
                return [
                    'title' => $reservation->service->name . ' - ' . $reservation->customer->name,
                    'start' => $reservation->start_time,
                    'end' => $reservation->end_time,
                    'id' => $reservation->id,
                    'className' => $colors[$index % count($colors)],
                    'extendedProps' => [
                        'serviceName' => $reservation->service->name,
                        'customerName' => $reservation->customer->name,
                        'price' => number_format($reservation->service->price, 2, ',', '.') . ' €',
                        'duration' => $reservation->service->duration . ' minuti',
                    ]
                ];
            });
            return response()->json($events);
        })->name('reservations.data');
    });

    // --- Rotte per il Ruolo: Super Admin ---
    Route::prefix('superadmin')->name('superadmin.')->middleware('is.superadmin')->group(function () {
        Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/calendar', [SuperAdminDashboardController::class, 'calendar'])->name('calendar');
        Route::get('/reports', [SuperAdminDashboardController::class, 'reports'])->name('reports');

        Route::get('/reservations-data', function () {
            $reservations = Reservation::with('service.worker', 'customer')->get();
            $colors = ['event-blue', 'event-green', 'event-purple', 'event-orange'];
            $events = $reservations->map(function ($reservation, $index) use ($colors) {
                return [
                    'id' => $reservation->id,
                    'title' => $reservation->service->name . ' - ' . $reservation->customer->name,
                    'start' => $reservation->start_time,
                    'end' => $reservation->end_time,
                    'className' => $colors[$index % count($colors)],
                    'extendedProps' => [
                        'serviceName' => $reservation->service->name,
                        'customerName' => $reservation->customer->name,
                        'workerName' => $reservation->service->worker->name,
                    ]
                ];
            });
            return response()->json($events);
        })->name('reservations.data');
    });
});

/*
|--------------------------------------------------------------------------
| Rotte di Autenticazione
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';
