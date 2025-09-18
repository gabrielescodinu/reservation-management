<?php

namespace App\Mail;

use App\Models\Reservation; // <-- Importa il modello Reservation
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentReminder extends Mailable
{
    use Queueable, SerializesModels;

    // Proprietà pubblica per contenere i dati della prenotazione
    public Reservation $reservation;

    /**
     * Crea una nuova istanza del messaggio.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Definisce l'oggetto e il mittente dell'email.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Promemoria Appuntamento per Domani',
        );
    }

    /**
     * Definisce il contenuto dell'email.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.appointment-reminder',
        );
    }
}
