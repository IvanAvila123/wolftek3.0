<?php

namespace App\Livewire;

use App\Filament\Admin\Pages\Renovaciones;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RenovacionesStats extends StatsOverviewWidget
{
   protected function getStats(): array
    {
        // Instanciamos la página para poder acceder a sus métodos
        $page = new Renovaciones();

        return [
            Stat::make('Renovaciones Vencidas', $page->getVencidasCount())
                ->description('Líneas que ya han expirado')
                ->color('danger'),

            Stat::make('Renovaciones T1 (30 días)', $page->getT1Count())
                ->description('Vencen en los próximos 30 días')
                ->color('warning'),

            Stat::make('Renovaciones T2 (60 días)', $page->getT2Count())
                ->description('Vencen entre 31 y 60 días')
                ->color('info'),

            Stat::make('Renovaciones T3 (90 días)', $page->getT3Count())
                ->description('Vencen entre 61 y 90 días')
                ->color('success'),
        ];
    }
}
