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

    public function reports(): View
    {
        // --- DATI PER IL GRAFICO A LINEE (leggermente rinominato per chiarezza) ---
        $days = 30;
        $startDate = now()->subDays($days - 1)->startOfDay();
        $endDate = now()->endOfDay();
        $revenueData = Reservation::join('services', 'reservations.service_id', '=', 'services.id')
            ->where('reservations.status', 'confirmed')
            ->where('reservations.start_time', '<', now())
            ->whereBetween('reservations.start_time', [$startDate, $endDate])
            ->select(DB::raw('DATE(reservations.start_time) as date'), DB::raw('SUM(services.price) as total'))
            ->groupBy('date')->orderBy('date', 'asc')->pluck('total', 'date');

        $lineChartLabels = [];
        $lineChartData = [];
        for ($i = 0; $i < $days; $i++) {
            $date = now()->subDays($i)->format('Y-m-d');
            $lineChartLabels[] = Carbon::parse($date)->isoFormat('D MMM');
            $lineChartData[] = $revenueData[$date] ?? 0;
        }
        $lineChartLabels = array_reverse($lineChartLabels);
        $lineChartData = array_reverse($lineChartData);

        // --- NUOVA QUERY PER IL GRAFICO A TORTA ---
        // Calcoliamo il totale incassato per ogni servizio (solo i top 7 per leggibilità)
        $revenueByService = Reservation::join('services', 'reservations.service_id', '=', 'services.id')
            ->where('reservations.start_time', '<', now())
            ->where('reservations.status', 'confirmed')
            ->select('services.name', DB::raw('SUM(services.price) as total'))
            ->groupBy('services.name')
            ->orderBy('total', 'desc')
            ->limit(7)
            ->get();

        $pieChartLabels = $revenueByService->pluck('name');
        $pieChartData = $revenueByService->pluck('total');

        // Passiamo tutti i dati alla vista
        return view('superadmin.reports', compact('lineChartLabels', 'lineChartData', 'pieChartLabels', 'pieChartData'));
    }
}
