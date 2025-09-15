<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User; // <-- Assicurati di importare il modello User

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pulisce la tabella prima di popolarla per evitare duplicati
        // Utile se lanci il seeder più volte
        // User::truncate();

        // Password comune per tutti gli utenti di test
        $password = Hash::make('password');

        // --- CREAZIONE DEI WORKER ---
        User::create([
            'name' => 'Mario Rossi',
            'email' => 'worker1@example.com',
            'password' => $password,
            'role' => 'worker',
        ]);

        User::create([
            'name' => 'Luigi Verdi',
            'email' => 'worker2@example.com',
            'password' => $password,
            'role' => 'worker',
        ]);

        // --- CREAZIONE DEI CUSTOMER ---
        User::create([
            'name' => 'Paola Bianchi',
            'email' => 'customer1@example.com',
            'password' => $password,
            'role' => 'customer',
        ]);

        User::create([
            'name' => 'Giulia Neri',
            'email' => 'customer2@example.com',
            'password' => $password,
            'role' => 'customer',
        ]);
    }
}
