<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Validation\Rule;
use Illuminate\Http\RedirectResponse;

class ServiceController extends Controller
{
    public function index(): View
    {
        $services = Service::with('worker')->latest()->get();
        return view('services.index', compact('services'));
    }

    public function show(Service $service): View
    {
        return view('services.show', compact('service'));
    }

    public function create()
    {
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Azione non autorizzata.');
        }
        return view('services.create');
    }

    public function store(Request $request)
    {
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Azione non autorizzata.');
        }
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('services')],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
        ]);
        Auth::user()->services()->create($validated);
        return redirect()->route('services.index')->with('status', 'Servizio creato con successo!');
    }

    public function edit(Service $service)
    {
        
        $this->authorize('update', $service);
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('services')->ignore($service->id)],
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'duration' => 'required|integer|min:1',
        ]);
        $service->update($validated);
        return redirect()->route('services.index')->with('status', 'Servizio aggiornato con successo!');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service); // Assumendo che aggiungerai una policy 'delete'
        $service->delete();
        return redirect()->route('services.index')->with('status', 'Servizio eliminato con successo!');
    }
}
