<?php

// in tests/Feature/AppointmentReminderTest.php

use App\Models\User;
use App\Models\Service;
use App\Models\Reservation;
use App\Mail\AppointmentReminder;
use Illuminate\Support\Facades\Mail;
use function Pest\Laravel\artisan;

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);

// Questo blocco viene eseguito prima di ogni test.
beforeEach(function () {
    // 1. Attiviamo il "finto" sistema di email di Laravel
    Mail::fake();

    // 2. Creiamo i dati di base che ci serviranno
    $this->customer = User::factory()->create(['role' => 'customer']);
    $this->worker = User::factory()->create(['role' => 'worker']);
    $this->service = Service::factory()->create(['user_id' => $this->worker->id]);
});

test('il comando invia un promemoria per un appuntamento di domani', function () {
    // Prepara: Crea una prenotazione per domani
    $reservation = Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->addDay()->setHour(10),
    ]);

    // Agisci: Esegui il nostro comando Artisan
    artisan('app:send-appointment-reminders')->assertExitCode(0);

    // Verifica: Controlla che UN'email sia stata inviata
    Mail::assertSent(AppointmentReminder::class, 1);

    // Verifica (più in dettaglio) che l'email sia stata inviata al cliente corretto
    Mail::assertSent(AppointmentReminder::class, function (AppointmentReminder $mail) {
        return $mail->hasTo($this->customer->email);
    });
});

test('il comando non invia promemoria per appuntamenti oltre domani', function () {
    // Prepara: Crea una prenotazione per dopodomani
    Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->addDays(2),
    ]);

    // Agisci: Esegui il comando
    artisan('app:send-appointment-reminders');

    // Verifica: Controlla che NESSUNA email sia stata inviata
    Mail::assertNothingSent();
});

test('il comando non invia promemoria per appuntamenti passati', function () {
    // Prepara: Crea una prenotazione per ieri
    Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->subDay(),
    ]);

    artisan('app:send-appointment-reminders');

    Mail::assertNothingSent();
});

test('lemail di promemoria contiene le informazioni corrette', function () {
    $reservation = Reservation::factory()->create([
        'customer_id' => $this->customer->id,
        'service_id' => $this->service->id,
        'start_time' => now()->addDay()->setHour(14)->setMinute(30),
    ]);

    // Istanzia direttamente la classe Mailable per ispezionarla
    $mailable = new AppointmentReminder($reservation);

    // Verifica che l'email contenga il nome del cliente e del servizio
    $mailable->assertSeeInHtml($this->customer->name);
    $mailable->assertSeeInHtml($this->service->name);
    $mailable->assertSeeInHtml('14:30'); // Verifica l'orario

    // Verifica l'oggetto dell'email
    $mailable->assertHasSubject('Promemoria Appuntamento per Domani');
});
