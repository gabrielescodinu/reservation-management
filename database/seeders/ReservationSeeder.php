<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use Carbon\Carbon;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pulisce la tabella prima di popolarla
        // Rimuovilo se gestisci il truncate nel DatabaseSeeder.php
        Reservation::truncate();

        // Recupera utenti e servizi
        $customers = User::where('role', 'customer')->get();
        $worker1 = User::where('email', 'worker1@example.com')->first();
        $service1 = Service::where('user_id', $worker1->id)->first();

        if (!$customers->count() || !$worker1 || !$service1) {
            $this->command->info('Utenti o servizi non trovati. Salto il ReservationSeeder.');
            return;
        }

        // Crea alcune prenotazioni di esempio
        $this->createReservation(
            $customers->first()->id,
            $service1,
            Carbon::today()->addDays(2)->setHour(10)->setMinute(0)
        );

        $this->createReservation(
            $customers->last()->id,
            $service1,
            Carbon::today()->addDays(2)->setHour(14)->setMinute(30)
        );

        $this->createReservation(
            $customers->first()->id,
            $service1,
            Carbon::today()->addDays(4)->setHour(9)->setMinute(0)
        );
    }

    /**
     * Funzione helper per creare una prenotazione
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
