<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\WorkerDashboardController; // Creeremo questo controller tra poco
use App\Http\Controllers\ReservationController;

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
    // ... tutte le tue altre rotte

    // NUOVA ROTTA: Fornisce i dati delle prenotazioni al calendario
    Route::get('/worker/reservations-data', function () {
        // Assicurati che l'utente sia un worker
        if (auth()->user()->role !== 'worker') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Recupera l'utente worker autenticato
        $worker = auth()->user();

        // Carica le sue prenotazioni usando la relazione che abbiamo definito.
        // 'with()' pre-carica i dati del servizio e del cliente per efficienza (evita query N+1)
        $reservations = $worker->reservationsAsWorker()->with('service', 'customer')->get();

        // Formatta i dati nel formato che FullCalendar si aspetta
        $events = $reservations->map(function ($reservation) {
            return [
                'title' => $reservation->service->name . ' - ' . $reservation->customer->name,
                'start' => $reservation->start_time,
                'end'   => $reservation->end_time,
                'id'    => $reservation->id,
            ];
        });

        // Restituisci i dati come risposta JSON
        return response()->json($events);
    })->name('worker.reservations.data'); // Diamo un nome alla rotta

    Route::resource('reservations', ReservationController::class)->only(['store']);
});

require __DIR__.'/auth.php';
