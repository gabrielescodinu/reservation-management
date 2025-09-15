<?php

namespace Database\Factories;

use App\Models\User; // <-- Importa User
use Illuminate\Database\Eloquent\Factories\Factory;

class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Se non viene specificato un user_id, ne crea uno nuovo con ruolo 'worker'
            'user_id' => User::factory(['role' => 'worker']),
            'name' => $this->faker->words(3, true), // es. "Taglio Barba Professionale"
            'description' => $this->faker->sentence(),
            'price' => $this->faker->randomFloat(2, 10, 100), // Prezzo tra 10 e 100
            'duration' => $this->faker->randomElement([30, 45, 60, 90]), // Durata in minuti
        ];
    }
}
