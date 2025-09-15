<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CustomerDashboardController extends Controller
{
    /**
     * Mostra la dashboard del cliente con le sue prenotazioni.
     */
    public function index(): View
    {
        // Recupera l'utente autenticato
        $customer = Auth::user();

        // Carica le prenotazioni del cliente, ordinate dalla più recente.
        // Usiamo with() per caricare in anticipo i dati del servizio e del worker,
        // evitando query multiple e rendendo tutto più efficiente (Eager Loading).
        $reservations = $customer->reservationsAsCustomer()
            ->with('service.worker')
            ->orderBy('start_time', 'desc')
            ->get();

        // Passa i dati alla vista
        return view('customer.dashboard', compact('reservations'));
    }
}
