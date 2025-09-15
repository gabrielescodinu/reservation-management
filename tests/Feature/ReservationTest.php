<?php

// in tests/Feature/ReservationTest.php

use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use Carbon\Carbon;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

// Il blocco beforeEach viene eseguito prima di ogni test in questo file
beforeEach(function () {
    // Creiamo un worker e un cliente per i nostri test
    $this->worker = User::factory()->create(['role' => 'worker']);
    $this->customer = User::factory()->create(['role' => 'customer']);

    // Creiamo un servizio appartenente al worker
    $this->service = Service::factory()->create([
        'user_id' => $this->worker->id,
        'duration' => 60, // Durata di 60 minuti
    ]);
});

it('un cliente può prenotare un servizio in uno slot disponibile', function () {
    $startTime = Carbon::tomorrow()->setHour(10);

    actingAs($this->customer)
        ->post(route('reservations.store'), [
            'service_id' => $this->service->id,
            'start_time' => $startTime->toDateTimeString(),
        ])
        ->assertRedirect()
        ->assertSessionHas('status', 'Prenotazione confermata con successo!');

    // Verifica che la prenotazione sia stata creata nel database
    $this->assertDatabaseHas('reservations', [
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => $startTime->toDateTimeString(),
    ]);
});

it('un cliente non può prenotare un servizio in uno slot già occupato', function () {
    $startTime = Carbon::tomorrow()->setHour(14);
    $endTime = $startTime->copy()->addMinutes($this->service->duration);

    // 1. Crea una prenotazione esistente
    Reservation::create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => $startTime,
        'end_time' => $endTime,
    ]);

    // 2. Prova a prenotare uno slot che si sovrappone (inizia 30 minuti dopo l'inizio del primo)
    actingAs($this->customer)
        ->post(route('reservations.store'), [
            'service_id' => $this->service->id,
            'start_time' => $startTime->copy()->addMinutes(30)->toDateTimeString(),
        ])
        ->assertSessionHasErrors('start_time'); // Verifica che ci sia l'errore di validazione

    // 3. Assicurati che sia presente solo la prenotazione originale
    $this->assertDatabaseCount('reservations', 1);
});

it('un cliente non può prenotare un servizio nel passato', function () {
    actingAs($this->customer)
        ->post(route('reservations.store'), [
            'service_id' => $this->service->id,
            'start_time' => now()->subHour()->toDateTimeString(),
        ])
        ->assertSessionHasErrors('start_time'); // Verifica la regola di validazione 'after:now'
});

it('un worker non può prenotare un proprio servizio', function () {
    actingAs($this->worker)
        ->post(route('reservations.store'), [
            'service_id' => $this->service->id,
            'start_time' => now()->addDay()->toDateTimeString(),
        ])
        ->assertSessionHasErrors('service_id');
});
