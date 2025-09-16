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
        $workers = User::where('role', 'worker')->with('services')->get();
        $customers = User::where('role', 'customer')->get();

        if ($workers->isEmpty() || $customers->isEmpty()) {
            $this->command->info('Nessun worker o customer trovato. Salto il ReservationSeeder.');
            return;
        }

        // --- CREAZIONE PRENOTAZIONI PASSATE ---
        $this->command->info('Creazione di prenotazioni passate...');
        $this->createReservationsInTimeRange(
            150, // Numero di prenotazioni da creare
            365, // Periodo di tempo in giorni
            'past', // Specifica che sono nel passato
            $workers,
            $customers
        );

        // --- CREAZIONE PRENOTAZIONI FUTURE ---
        $this->command->info('Creazione di prenotazioni future...');
        $this->createReservationsInTimeRange(
            50,  // Numero di prenotazioni da creare
            60,  // Periodo di tempo in giorni
            'future', // Specifica che sono nel futuro
            $workers,
            $customers
        );

        $this->command->info('Prenotazioni casuali create con successo.');
    }

    /**
     * Funzione helper per creare prenotazioni in un dato intervallo di tempo (passato o futuro).
     */
    private function createReservationsInTimeRange(int $count, int $daysRange, string $direction, $workers, $customers): void
    {
        for ($i = 0; $i < $count; $i++) {
            $worker = $workers->random();
            $customer = $customers->random();

            if ($worker->services->isEmpty()) {
                continue;
            }
            $service = $worker->services->random();

            $maxAttempts = 10;
            for ($attempt = 0; $attempt < $maxAttempts; $attempt++) {
                $randomDay = rand(1, $daysRange);
                $randomHour = rand(8, 18);
                $randomMinute = rand(0, 1) ? 0 : 30;

                // Determina se aggiungere o sottrarre giorni in base alla direzione
                if ($direction === 'past') {
                    $startTime = Carbon::today()->subDays($randomDay)->setHour($randomHour)->setMinute($randomMinute);
                } else {
                    $startTime = Carbon::today()->addDays($randomDay)->setHour($randomHour)->setMinute($randomMinute);
                }

                $endTime = $startTime->copy()->addMinutes($service->duration);

                // Controllo anti-sovrapposizione
                $isOccupied = Reservation::whereHas('service', function ($query) use ($worker) {
                    $query->where('user_id', $worker->id);
                })->where(function ($query) use ($startTime, $endTime) {
                    $query->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                })->exists();

                if (!$isOccupied) {
                    Reservation::create([
                        'customer_id' => $customer->id,
                        'service_id' => $service->id,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                    break;
                }
            }
        }
    }
}
