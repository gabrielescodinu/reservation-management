<?php

// in tests/Feature/SuperAdminDashboardTest.php

use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\delete;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

// Questo blocco viene eseguito prima di ogni test.
// Prepara un ambiente di test con tutti i ruoli e delle prenotazioni multiple.
beforeEach(function () {
    $this->superadmin = User::factory()->create(['role' => 'superadmin']);
    $this->worker = User::factory()->create(['role' => 'worker']);
    $this->customer1 = User::factory()->create(['role' => 'customer']);
    $this->customer2 = User::factory()->create(['role' => 'customer']);

    $this->service = Service::factory()->create(['user_id' => $this->worker->id]);

    // Creiamo due prenotazioni da due clienti diversi
    $this->reservation1 = Reservation::factory()->create([
        'service_id' => $this->service->id,
        'customer_id' => $this->customer1->id,
    ]);
    $this->reservation2 = Reservation::factory()->create([
        'service_id' => $this->service->id,
        'customer_id' => $this->customer2->id,
    ]);
});

// --- TEST DI AUTORIZZAZIONE ---

test('solo il super admin può accedere alla dashboard a tabella', function () {
    actingAs($this->superadmin)
        ->get(route('superadmin.dashboard'))
        ->assertOk();

    actingAs($this->worker)
        ->get(route('superadmin.dashboard'))
        ->assertForbidden();

    actingAs($this->customer1)
        ->get(route('superadmin.dashboard'))
        ->assertForbidden();
});

test('solo il super admin può accedere alla vista calendario', function () {
    actingAs($this->superadmin)
        ->get(route('superadmin.calendar'))
        ->assertOk();

    actingAs($this->worker)
        ->get(route('superadmin.calendar'))
        ->assertForbidden();
});


// --- TEST FUNZIONALI ---

test('la dashboard a tabella del super admin mostra tutte le prenotazioni', function () {
    actingAs($this->superadmin)
        ->get(route('superadmin.dashboard'))
        ->assertOk()
        // Verifica di vedere i nomi di entrambi i clienti, provando che non c'è filtro
        ->assertSee($this->customer1->name)
        ->assertSee($this->customer2->name);
});

test('il data endpoint del super admin restituisce tutte le prenotazioni', function () {
    actingAs($this->superadmin)
        ->getJson(route('superadmin.reservations.data'))
        ->assertOk()
        // Verifica che l'API restituisca 2 prenotazioni, come create nel setup
        ->assertJsonCount(2);
});

test('il super admin può cancellare la prenotazione di un altro utente', function () {
    // Verifica che la prenotazione esista
    $this->assertDatabaseHas('reservations', ['id' => $this->reservation1->id]);

    // Agisci come superadmin e cancella la prenotazione del customer1
    actingAs($this->superadmin)
        ->delete(route('reservations.destroy', $this->reservation1))
        ->assertRedirect()
        ->assertSessionHas('status', 'Prenotazione annullata con successo!');

    // Verifica che la prenotazione sia stata effettivamente rimossa dal database
    $this->assertDatabaseMissing('reservations', ['id' => $this->reservation1->id]);
});
