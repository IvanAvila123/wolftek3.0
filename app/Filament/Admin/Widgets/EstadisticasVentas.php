<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Oportunidad;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class EstadisticasVentas extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $inicioMes = Carbon::now()->startOfMonth();
        $finMes = Carbon::now()->endOfMonth();

        $ventasMesActual = Oportunidad::where('estado', 'orden_entregada')
            ->whereBetween('created_at', [$inicioMes, $finMes])
            ->count();

        $ventasMesPasado = Carbon::now()->subMonth()->startOfMonth();
        $finMesPasado = Carbon::now()->subMonth()->endOfMonth();
        $ventasMesAnterior = Oportunidad::where('estado', 'orden_entregada')
            ->whereBetween('updated_at', [$ventasMesPasado, $finMesPasado])
            ->count();
        $descripcionCambio = "vs mes anterior";
        $cambioPorcentaje = 0;
        if ($ventasMesAnterior > 0) {
            $cambioPorcentaje = (($ventasMesActual - $ventasMesAnterior) / $ventasMesAnterior) * 100;
            $descripcionCambio = sprintf('%+.1f%% vs mes anterior', $cambioPorcentaje);
        }elseif ($ventasMesActual > 0) {
            $descripcionCambio = 'Nuevo en el mes';
        }
        return [
            Stat::make('Ventas Entregadas (Mes Actual)', $ventasMesActual)
                ->description($descripcionCambio)
                 // Icono basado en el cambio
                ->descriptionIcon($cambioPorcentaje >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                 // Color basado en el cambio
                ->color($cambioPorcentaje >= 0 ? 'success' : 'danger'),

            Stat::make('Ventas Pendientes', Oportunidad::where('estado', 'Pendiente')->count()),
            Stat::make('Ventas Rechazadas', Oportunidad::where('estado', 'rechazada')->count()),
            Stat::make('Ventas Canceladas', Oportunidad::where('estado', 'cancelada')->count()),
            Stat::make('Total Ventas', Oportunidad::count()),
            Stat::make('Ventas Apunto de Entregar', Oportunidad::where('estado', 'envios_ruta')->count()),
        ];
    }
}
