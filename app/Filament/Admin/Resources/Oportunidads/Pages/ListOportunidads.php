<?php

namespace App\Filament\Admin\Resources\Oportunidads\Pages;

use App\Filament\Admin\Resources\Oportunidads\OportunidadResource;
use App\Filament\Exports\OportunidadExporter;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListOportunidads extends ListRecords
{
    protected static string $resource = OportunidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Nueva Oportunidad'),
            ExportAction::make()
            ->label('Exportar Ventas')
            ->color('success')
            ->exporter(OportunidadExporter::class),
        ];
    }
}
