<?php

// in routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ...
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Aggiungi questo endpoint
Route::middleware('auth:sanctum')->get('/worker/reservations', function (Request $request) {
    $user = $request->user();

    if ($user->role !== 'worker') {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    // Recupera le prenotazioni del worker e formattale per FullCalendar
    $reservations = $user->reservationsAsWorker()->with('service', 'customer')->get();

    $events = $reservations->map(function ($reservation) {
        return [
            'title' => $reservation->service->name . ' - ' . $reservation->customer->name,
            'start' => $reservation->start_time,
            'end' => $reservation->end_time,
            'id' => $reservation->id,
        ];
    });

    return response()->json($events);
});