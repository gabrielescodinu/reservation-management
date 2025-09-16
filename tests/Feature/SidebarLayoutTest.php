<?php

// in tests/Feature/SidebarLayoutTest.php

use App\Models\User;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

test('un worker vede i link corretti nella sidebar', function () {
    // 1. Prepara: Crea un utente worker
    $worker = User::factory()->create(['role' => 'worker']);

    // 2. Agisci: Fai il login come worker e visita la dashboard
    actingAs($worker)
        ->get(route('dashboard'))
        ->assertOk()
        // 3. Verifica: Controlla che i link corretti siano presenti
        ->assertSee('La Mia Agenda')
        ->assertSee('I Miei Servizi')
        // E che i link del customer NON siano presenti
        ->assertDontSee('Le Mie Prenotazioni')
        ->assertDontSee('Trova Servizi');
});

test('un cliente vede i link corretti nella sidebar', function () {
    // 1. Prepara: Crea un utente customer
    $customer = User::factory()->create(['role' => 'customer']);

    // 2. Agisci: Fai il login come customer e visita la dashboard
    actingAs($customer)
        ->get(route('dashboard'))
        ->assertOk()
        // 3. Verifica: Controlla che i link corretti siano presenti
        ->assertSee('Le Mie Prenotazioni')
        ->assertSee('Trova Servizi')
        // E che i link del worker NON siano presenti
        ->assertDontSee('La Mia Agenda');
});

test('tutti gli utenti autenticati vedono i link comuni', function () {
    // Usiamo un customer per questo test, ma funzionerebbe anche con un worker
    $customer = User::factory()->create(['role' => 'customer']);

    actingAs($customer)
        ->get(route('dashboard'))
        ->assertOk()
        // Verifica la presenza dei link comuni a tutti i ruoli
        ->assertSee('Dashboard')
        ->assertSee('Account');
});
