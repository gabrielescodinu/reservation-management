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

    // in SuperAdminDashboardController.php

    public function reports(Request $request): View
    {
        // 1. Legge mese e anno dalla richiesta, o usa quelli correnti come default
        $selectedYear = $request->input('year', now()->year);
        $selectedMonth = $request->input('month', now()->month);

        // 2. Crea le date di inizio e fine per il mese selezionato
        $startDate = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        // --- DATI PER IL GRAFICO A LINEE (ora dinamico) ---
        $revenueData = Reservation::join('services', 'reservations.service_id', '=', 'services.id')
            ->where('reservations.status', 'confirmed')
            ->whereBetween('reservations.start_time', [$startDate, $endDate]) // Usa il range dinamico
            ->select(DB::raw('DATE(reservations.start_time) as date'), DB::raw('SUM(services.price) as total'))
            ->groupBy('date')->orderBy('date', 'asc')->pluck('total', 'date');

        // Prepara i dati per tutti i giorni del mese selezionato
        $daysInMonth = $startDate->daysInMonth;
        $lineChartLabels = [];
        $lineChartData = [];
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $date = Carbon::create($selectedYear, $selectedMonth, $day)->format('Y-m-d');
            $lineChartLabels[] = $day; // Etichetta solo con il numero del giorno
            $lineChartData[] = $revenueData[$date] ?? 0;
        }

        // --- DATI PER IL GRAFICO A TORTA (ora dinamico) ---
        $revenueByService = Reservation::join('services', 'reservations.service_id', '=', 'services.id')
            ->where('reservations.status', 'confirmed')
            ->whereBetween('reservations.start_time', [$startDate, $endDate]) // Usa il range dinamico
            ->select('services.name', DB::raw('SUM(services.price) as total'))
            ->groupBy('services.name')->orderBy('total', 'desc')->limit(7)->get();

        $pieChartLabels = $revenueByService->pluck('name');
        $pieChartData = $revenueByService->pluck('total');

        // 3. Passa tutti i dati, inclusi mese e anno selezionati, alla vista
        return view('superadmin.reports', compact(
            'lineChartLabels',
            'lineChartData',
            'pieChartLabels',
            'pieChartData',
            'selectedYear',
            'selectedMonth'
        ));
    }
}
