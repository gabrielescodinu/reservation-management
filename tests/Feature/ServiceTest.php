<?php

// in tests/Feature/ServiceTest.php

use App\Models\User;
use App\Models\Service;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

// Questo trait resetta il database dopo ogni test,
// garantendo che i test siano isolati e non interferiscano tra loro.
uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

// --- TEST DI AUTORIZZAZIONE ---

it('gli utenti non autenticati non possono vedere la pagina di creazione servizio', function () {
    // Fai una richiesta GET alla pagina di creazione senza essere loggato
    $response = get(route('services.create'));

    // Verifica di essere reindirizzato alla pagina di login
    $response->assertRedirect(route('login'));
});

it('i clienti (customer) non possono vedere la pagina di creazione servizio', function () {
    // Crea un utente con ruolo 'customer'
    $customer = User::factory()->create(['role' => 'customer']);

    // Esegui la richiesta "come se fossi" il cliente
    actingAs($customer)
        ->get(route('services.create'))
        ->assertForbidden(); // Verifica di ricevere un errore 403 Forbidden
});

it('i worker possono vedere la pagina di creazione servizio', function () {
    // Crea un utente con ruolo 'worker'
    $worker = User::factory()->create(['role' => 'worker']);

    // Esegui la richiesta come worker
    actingAs($worker)
        ->get(route('services.create'))
        ->assertStatus(200) // Verifica che la pagina venga caricata correttamente
        ->assertViewIs('services.create'); // Verifica che venga mostrata la vista giusta
});


// --- TEST FUNZIONALI ---

it('un worker può creare un nuovo servizio', function () {
    // Prepara i dati
    $worker = User::factory()->create(['role' => 'worker']);
    $serviceData = [
        'name' => 'Nuovo Servizio di Test',
        'description' => 'Una descrizione dettagliata.',
        'price' => 99.99,
        'duration' => 60,
    ];

    // Esegui la richiesta POST per salvare il servizio
    actingAs($worker)
        ->post(route('services.store'), $serviceData)
        ->assertRedirect(route('dashboard')); // Verifica il reindirizzamento

    // Verifica che il servizio sia stato salvato nel database
    // con i dati corretti e associato al worker giusto.
    $this->assertDatabaseHas('services', [
        'user_id' => $worker->id,
        'name' => 'Nuovo Servizio di Test',
        'price' => 99.99,
    ]);
});

it('la creazione del servizio fallisce se i dati non sono validi', function () {
    $worker = User::factory()->create(['role' => 'worker']);

    // Invia dati volutamente incompleti (manca il 'name')
    $invalidData = [
        'description' => 'Descrizione senza nome',
        'price' => 50,
        'duration' => 30,
    ];

    actingAs($worker)
        ->post(route('services.store'), $invalidData)
        ->assertSessionHasErrors(['name']); // Verifica che ci sia un errore di validazione per il campo 'name'

    // Assicurati che nulla sia stato salvato nel database
    $this->assertDatabaseCount('services', 0);
});
