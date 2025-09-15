<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Troviamo tutti gli utenti con ruolo 'worker'
        $workers = User::where('role', 'worker')->get();

        if ($workers->isEmpty()) {
            $this->command->info('Nessun utente "worker" trovato. Salto il ServiceSeeder.');
            return;
        }

        // Definiamo una lista di servizi standard per estetiste
        $servicesList = [
            [
                'name' => 'Manicure Estetica',
                'description' => 'Trattamento completo per unghie e mani, con applicazione di smalto classico.',
                'price' => 20.00,
                'duration' => 45,
            ],
            [
                'name' => 'Smalto Semipermanente',
                'description' => 'Applicazione di smalto semipermanente a lunga durata con lampada UV.',
                'price' => 35.00,
                'duration' => 60,
            ],
            [
                'name' => 'Pedicure Estetico',
                'description' => 'Trattamento di bellezza per piedi e unghie, con massaggio finale.',
                'price' => 35.00,
                'duration' => 60,
            ],
            [
                'name' => 'Pulizia Viso Profonda',
                'description' => 'Trattamento purificante e rigenerante per la pelle del viso con vapore e maschera specifica.',
                'price' => 50.00,
                'duration' => 75,
            ],
            [
                'name' => 'Massaggio Rilassante (50 min)',
                'description' => 'Massaggio total body con oli essenziali per alleviare stress e tensioni muscolari.',
                'price' => 60.00,
                'duration' => 50,
            ],
            [
                'name' => 'Epilazione Gambe e Inguine',
                'description' => 'Ceretta completa per gambe e zona inguine con cera delicata.',
                'price' => 40.00,
                'duration' => 45,
            ],
        ];

        // Usiamo un ciclo per assegnare tutti i servizi a ogni worker
        foreach ($workers as $worker) {
            foreach ($servicesList as $serviceData) {
                Service::create([
                    'user_id' => $worker->id,
                    'name' => $serviceData['name'],
                    'description' => $serviceData['description'],
                    'price' => $serviceData['price'],
                    'duration' => $serviceData['duration'],
                ]);
            }
        }

        $this->command->info('Servizi per estetiste creati per ' . $workers->count() . ' worker.');
    }
}
