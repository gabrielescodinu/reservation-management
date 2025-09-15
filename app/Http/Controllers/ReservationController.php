<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;


class ReservationController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validazione di base dei dati in input
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'start_time' => 'required|date|after:now',
        ]);

        // 2. Recupero delle informazioni necessarie
        $service = Service::findOrFail($request->service_id);
        $worker = $service->worker;
        $customer = Auth::user();

        // Assicurati che un worker non possa prenotare i propri servizi
        if ($customer->id === $worker->id) {
            throw ValidationException::withMessages([
                'service_id' => 'Non puoi prenotare un tuo stesso servizio.',
            ]);
        }

        $startTime = Carbon::parse($request->start_time);
        $endTime = $startTime->copy()->addMinutes($service->duration);

        // 3. IL CONTROLLO DI DISPONIBILITÀ (la logica chiave!)
        $isOccupied = $worker->reservationsAsWorker()
            ->where(function ($query) use ($startTime, $endTime) {
                // Controlla se una prenotazione esistente si sovrappone al nuovo slot
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->exists();

        if ($isOccupied) {
            // Se esiste già una prenotazione, lancia un errore di validazione
            throw ValidationException::withMessages([
                'start_time' => 'Il professionista è già occupato in questa fascia oraria. Scegli un altro orario.',
            ]);
        }

        // 4. Se il worker è libero, crea la prenotazione
        Reservation::create([
            'customer_id' => $customer->id,
            'service_id' => $service->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
        ]);

        // 5. Reindirizza con un messaggio di successo
        return redirect()->route('customer.dashboard')->with('status', 'Prenotazione confermata con successo!');
    }
}
