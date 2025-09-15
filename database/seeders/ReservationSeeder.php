<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Reservation;
use App\Models\Service;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        // Recupera tutti i worker con i loro servizi e tutti i clienti
        $workers = User::where('role', 'worker')->with('services')->get();
        $customers = User::where('role', 'customer')->get();

        if ($workers->isEmpty() || $customers->isEmpty()) {
            $this->command->info('Nessun worker o customer trovato. Salto il ReservationSeeder.');
            return;
        }

        $this->command->info('Creazione di prenotazioni casuali...');

        $reservationsToCreate = 30; // Numero di prenotazioni da creare
        $daysInFuture = 20; // Periodo di tempo in cui creare le prenotazioni (es. prossimi 20 giorni)

        for ($i = 0; $i < $reservationsToCreate; $i++) {
            // Seleziona un worker, un cliente e un servizio a caso
            $worker = $workers->random();
            $customer = $customers->random();

            // Assicurati che il worker abbia dei servizi
            if ($worker->services->isEmpty()) {
                continue; // Salta questo ciclo se il worker non ha servizi
            }
            $service = $worker->services->random();

            // Tenta di trovare uno slot libero per la prenotazione
            $maxAttempts = 10;
            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                // Genera una data e ora di inizio casuale
                $randomDay = rand(0, $daysInFuture);
                $randomHour = rand(8, 18); // Orario di lavoro tra le 8 e le 18
                $randomMinute = rand(0, 1) ? 0 : 30; // Inizia all'ora o alla mezz'ora

                $startTime = Carbon::today()->addDays($randomDay)->setHour($randomHour)->setMinute($randomMinute);
                $endTime = $startTime->copy()->addMinutes($service->duration);

                // Controlla se lo slot è già occupato per quel worker
                $isOccupied = Reservation::whereHas('service', function ($query) use ($worker) {
                    $query->where('user_id', $worker->id);
                })->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                })->exists();

                // Se lo slot è libero, crea la prenotazione e interrompi il ciclo di tentativi
                if (!$isOccupied) {
                    $this->createReservation($customer->id, $service, $startTime);
                    break;
                }
            }
        }

        $this->command->info('Prenotazioni casuali create con successo.');
    }

    /**
     * Funzione helper per creare una prenotazione (invariata)
     */
    private function createReservation(int $customerId, Service $service, Carbon $startTime): void
    {
        Reservation::create([
            'customer_id' => $customerId,
            'service_id' => $service->id,
            'start_time' => $startTime,
            'end_time' => $startTime->copy()->addMinutes($service->duration),
        ]);
    }
}
