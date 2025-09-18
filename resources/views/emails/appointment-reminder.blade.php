<x-mail::message>
# Promemoria del tuo appuntamento

Ciao **{{ $reservation->customer->name }}**,

Questo è un promemoria per il tuo appuntamento di domani.

**Servizio:** {{ $reservation->service->name }}
**Con:** {{ $reservation->service->worker->name }}
**Quando:** {{ \Carbon\Carbon::parse($reservation->start_time)->isoFormat('dddd D MMMM YYYY') }} alle **{{ \Carbon\Carbon::parse($reservation->start_time)->format('H:i') }}**

Ti aspettiamo!

Grazie,<br>
{{ config('app.name') }}
</x-mail::message>