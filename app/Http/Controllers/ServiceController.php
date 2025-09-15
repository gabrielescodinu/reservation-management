<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(): View
    {
        // Carichiamo tutti i servizi con la relazione 'worker' per evitare query N+1
        $services = Service::with('worker')->latest()->get();

        return view('services.index', compact('services'));
    }

    public function show(Service $service): View
    {
        return view('services.show', compact('service'));
    }

    /**
     * Mostra il form per creare un nuovo servizio.
     */
    public function create()
    {
        // AGGIUNGI QUESTO CONTROLLO
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Azione non autorizzata.');
        }

        return view('services.create');
    }

    /**
     * Salva un nuovo servizio nel database.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Azione non autorizzata.');
        }

        // 1. Validazione dei dati
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
        ]);

        // 2. Aggiunge l'ID dell'utente loggato ai dati validati
        $validated['user_id'] = Auth::id();

        // 3. Crea il servizio
        Service::create($validated);

        // 4. Reindirizza l'utente con un messaggio di successo
        return redirect()->route('dashboard')->with('status', 'Servizio creato con successo!');
    }

    // ... altri metodi
}
