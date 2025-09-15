<?php

// in tests/Feature/WorkerDashboardTest.php

use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;


uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->worker = User::factory()->create(['role' => 'worker']);
    $this->customer = User::factory()->create(['role' => 'customer']);
    $this->service = Service::factory()->create(['user_id' => $this->worker->id]);

    // Crea una prenotazione per il nostro worker
    $this->reservation = Reservation::factory()->create([
        'service_id' => $this->service->id,
        'customer_id' => $this->customer->id,
    ]);
});

it('un worker può accedere alla sua dashboard', function () {
    actingAs($this->worker)
        ->get(route('worker.dashboard'))
        ->assertOk() // Status 200
        ->assertViewIs('worker.dashboard');
});

it('un cliente non può accedere alla dashboard del worker', function () {
    actingAs($this->customer)
        ->get(route('worker.dashboard'))
        ->assertForbidden(); // Status 403
});

it('il data endpoint del worker restituisce le sue prenotazioni', function () {
    $response = actingAs($this->worker)
        ->getJson(route('worker.reservations.data'))
        ->assertOk();

    // Verifica che ci sia 1 evento nel JSON di risposta
    $response->assertJsonCount(1);

    // Verifica che il JSON contenga il titolo del servizio prenotato
    $response->assertJsonFragment([
        'title' => $this->service->name . ' - ' . $this->customer->name,
    ]);
});

it('il data endpoint non fornisce dati ad altri utenti', function () {
    // Un altro worker non dovrebbe vedere le prenotazioni del primo
    $anotherWorker = User::factory()->create(['role' => 'worker']);

    actingAs($anotherWorker)
        ->getJson(route('worker.reservations.data'))
        ->assertOk()
        ->assertJsonCount(0); // Si aspetta un array vuoto
});
