<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Service;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Prende un servizio a caso (che a sua volta creerà un worker se non esiste)
        $service = Service::factory()->create();
        $startTime = Carbon::instance($this->faker->dateTimeBetween('+1 day', '+1 month'));
        $endTime = $startTime->copy()->addMinutes($service->duration);

        return [
            // Crea un nuovo cliente se non specificato
            'customer_id' => User::factory(['role' => 'customer']),
            'service_id' => $service->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmed',
        ];
    }
}
