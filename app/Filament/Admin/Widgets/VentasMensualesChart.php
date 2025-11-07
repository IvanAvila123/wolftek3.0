<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Oportunidad;
use Carbon\Carbon;
use Filament\Widgets\ChartWidget;

class VentasMensualesChart extends ChartWidget
{
    protected ?string $heading = 'Ventas Mensuales Por Mes';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = Oportunidad::selectRaw('DATE_FORMAT(updated_at, "%Y-%m") as mes, COUNT(*) as total')
            ->where('estado', 'orden_entregada')
            ->where('updated_at', '>=', Carbon::now()->subMonths(5)->startOfMonth()) // Últimos 6 meses
            ->groupBy('mes')
            ->orderBy('mes', 'asc')
            ->pluck('total', 'mes')
            ->toArray();
        $meses = [];
        $labels = [];
        for ($i = 5; $i >= 0; $i--) {
            $carbonDate = Carbon::now()->subMonths($i);
            $mesKey = $carbonDate->format('Y-m');
            $labels[] = $carbonDate->format('M Y'); // Formato "Oct 2025"
            $meses[$mesKey] = $data[$mesKey] ?? 0;
        }
        return [
            'datasets' => [
                [
                    'label' => 'Ventas Entregadas',
                    'data' => array_values($meses),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
