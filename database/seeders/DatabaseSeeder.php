<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Service; // <-- Importa Service
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema; // <-- Importa Schema

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Disabilita temporaneamente i controlli sulle foreign key
        Schema::disableForeignKeyConstraints();

        // 2. Svuota le tabelle (prima la tabella "figlia")
        Service::truncate();
        User::truncate();

        // 3. Riabilita i controlli
        Schema::enableForeignKeyConstraints();

        // 4. Esegui i seeder per popolare le tabelle
        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            ReservationSeeder::class,
        ]);
    }
}
