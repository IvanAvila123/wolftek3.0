<?php

namespace App\Filament\Admin\Resources\RevisionOportunidads\Pages;

use App\Filament\Admin\Resources\RevisionOportunidads\RevisionOportunidadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListRevisionOportunidads extends ListRecords
{
    protected static string $resource = RevisionOportunidadResource::class;
    protected static ?string $title = 'Revisiones de Oportunidades';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
            ->label('Crear Revisión'),
        ];
    }
}
