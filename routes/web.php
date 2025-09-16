<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkerDashboardController;
use App\Http\Controllers\ReservationController;
use App\Models\Service;
use App\Http\Controllers\CustomerDashboardController;

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

    Route::get('/worker/dashboard', function () {
        if (auth()->user()->role !== 'worker') {
            abort(403);
        }
        return view('worker.dashboard');
    })->name('worker.dashboard');
});

// in routes/web.php

Route::middleware('auth')->group(function () {
    // NUOVA ROTTA: Fornisce i dati delle prenotazioni al calendario
    Route::get('/worker/reservations-data', function () {
        // Assicurati che l'utente sia un worker
        if (auth()->user()->role !== 'worker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $worker = auth()->user();
        $reservations = $worker->reservationsAsWorker()->with('service', 'customer')->get();

        // Definiamo una palette di colori per le nostre classi CSS
        $colors = ['event-blue', 'event-green', 'event-purple', 'event-orange'];

        $events = $reservations->map(function ($reservation, $index) use ($colors) {
            return [
                'title' => $reservation->service->name . ' - ' . $reservation->customer->name,
                'start' => $reservation->start_time,
                'end'   => $reservation->end_time,
                'id'    => $reservation->id,
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

    })->name('worker.reservations.data'); // Diamo un nome alla rotta

    Route::get('/my-reservations', [CustomerDashboardController::class, 'index'])
        ->name('customer.dashboard');

    Route::resource('reservations', ReservationController::class)->only(['store', 'destroy']);
    
    Route::get('/services/{service}/reservations', function (Service $service) {
        $reservations = $service->reservations()
            ->where('start_time', '>=', now())
            ->get();

        $events = $reservations->map(function ($reservation) {
            return [
                'title' => 'Occupato', // Non mostriamo i dettagli agli altri clienti
                'start' => $reservation->start_time,
                'end' => $reservation->end_time,
                'backgroundColor' => '#d1d5db', // Grigio
                'borderColor' => '#d1d5db',
            ];
        });

        return response()->json($events);
    })->name('services.reservations');
});

require __DIR__.'/auth.php';
