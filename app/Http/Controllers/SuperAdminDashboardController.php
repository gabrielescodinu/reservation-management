<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperAdminDashboardController extends Controller
{
    public function index(): View
    {
        // Recupera tutte le prenotazioni, dalla più recente, con tutti i dati correlati
        $reservations = Reservation::with('service.worker', 'customer')
            ->orderBy('start_time', 'desc')
            ->paginate(15); // Usiamo la paginazione perché potrebbero essere molte

        return view('superadmin.dashboard', compact('reservations'));
    }

    public function calendar(): View
    {
        return view('superadmin.calendar');
    }
}
