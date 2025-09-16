<?php

// in tests/Feature/CustomerDashboardTest.php

use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->customer = User::factory()->create(['role' => 'customer']);
    $this->worker = User::factory()->create(['role' => 'worker']);
    $this->service = Service::factory()->create(['user_id' => $this->worker->id]);
});

test('mostra lo stato "Effettuata" per le prenotazioni passate', function () {
    // Crea una prenotazione nel passato
    Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->subDay(),
    ]);

    actingAs($this->customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('Effettuata')
        ->assertDontSee('Confermata');
});

test('nasconde il pulsante di annullamento per le prenotazioni passate', function () {
    Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->subDay(),
    ]);

    actingAs($this->customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertDontSee('Annulla Prenotazione');
});

test('mostra il pulsante di annullamento per le prenotazioni future', function () {
    Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->addDay(),
    ]);

    actingAs($this->customer)
        ->get(route('customer.dashboard'))
        ->assertOk()
        ->assertSee('Annulla Prenotazione');
});
