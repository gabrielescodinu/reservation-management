<?php

namespace App\Console\Commands;

use App\Mail\AppointmentReminder;
use App\Models\Reservation;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAppointmentReminders extends Command
{
    /**
     * La firma del comando, usata per chiamarlo.
     */
    protected $signature = 'app:send-appointment-reminders';

    /**
     * La descrizione del comando.
     */
    protected $description = 'Invia email di promemoria per gli appuntamenti del giorno successivo.';

    /**
     * Esegue la logica del comando.
     */
    public function handle()
    {
        $this->info('Inizio ricerca appuntamenti per domani...');

        // Definiamo l'intervallo di "domani" (dalla mezzanotte alla fine del giorno)
        $tomorrowStart = now()->addDay()->startOfDay();
        $tomorrowEnd = now()->addDay()->endOfDay();

        // Troviamo tutte le prenotazioni confermate per domani
        $reservations = Reservation::with(['customer', 'service.worker'])
            ->where('status', 'confirmed')
            ->whereBetween('start_time', [$tomorrowStart, $tomorrowEnd])
            ->get();

        if ($reservations->isEmpty()) {
            $this->info('Nessun appuntamento trovato per domani. Uscita.');
            return 0;
        }

        $this->info("Trovati {$reservations->count()} appuntamenti. Invio promemoria...");

        // Inviamo un'email per ogni prenotazione trovata
        foreach ($reservations as $reservation) {
            Mail::to($reservation->customer->email)->send(new AppointmentReminder($reservation));
            $this->line("Promemoria inviato a: {$reservation->customer->email}");
        }

        // in produzione usare queue per inviare le email in background
        // foreach ($reservations as $reservation) {
        //     Mail::to($reservation->customer->email)->queue(new AppointmentReminder($reservation));
        //     $this->line("Promemoria accodato per: {$reservation->customer->email}");
        // }

        $this->info('Tutti i promemoria sono stati inviati con successo!');
        return 0;
    }
}
