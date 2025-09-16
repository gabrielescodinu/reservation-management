<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

    public function reports(): View
    {
        // Calcoliamo gli incassi degli ultimi 30 giorni
        $days = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();

        $revenueData = Reservation::join('services', 'reservations.service_id', '=', 'services.id')
            ->where('reservations.status', 'confirmed')
            ->where('reservations.start_time', '<', now()) // Solo prenotazioni passate
            ->whereBetween('reservations.start_time', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(reservations.start_time) as date'),
                DB::raw('SUM(services.price) as total')
            )
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->pluck('total', 'date'); // Otteniamo un array associativo [date => total]

        // Prepariamo i dati per il grafico, assicurandoci di avere uno zero per i giorni senza incassi
        $labels = [];
        $data = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->isoFormat('D MMM'); // Formato "16 Set"
            $data[] = $revenueData[$date] ?? 0;
        }

        // I dati sono in ordine cronologico inverso, quindi li giriamo
        $labels = array_reverse($labels);
        $data = array_reverse($data);

        return view('superadmin.reports', compact('labels', 'data'));
    }
}
