<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;   // <-- Importa User
use App\Models\Service; // <-- Importa Service

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pulisce la tabella prima di popolarla
        // Service::truncate();

        // Troviamo gli utenti con ruolo 'worker'
        $workers = User::where('role', 'worker')->get();

        if ($workers->isEmpty()) {
            $this->command->info('Nessun utente "worker" trovato. Salto il ServiceSeeder.');
            return;
        }

        // Creiamo i servizi e li associamo ai worker
        Service::create([
            'user_id' => $workers->first()->id, // Assegna al primo worker
            'name' => 'Taglio Capelli Uomo',
            'description' => 'Taglio classico con shampoo e styling.',
            'price' => 25.00,
            'duration' => 30, // in minuti
        ]);

        Service::create([
            'user_id' => $workers->first()->id, // Assegna al primo worker
            'name' => 'Rasatura Barba',
            'description' => 'Rasatura tradizionale con panno caldo.',
            'price' => 15.00,
            'duration' => 20,
        ]);

        if (isset($workers[1])) {
            Service::create([
                'user_id' => $workers[1]->id, // Assegna al secondo worker
                'name' => 'Consulenza Web',
                'description' => 'Consulenza di un\'ora per il tuo progetto web.',
                'price' => 80.00,
                'duration' => 60,
            ]);
        }
    }
}
