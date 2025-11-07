<?php

namespace App\Filament\Admin\Resources\RevisionOportunidads\Pages;

use App\Filament\Admin\Resources\RevisionOportunidads\RevisionOportunidadResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewRevisionOportunidad extends ViewRecord
{
    protected static string $resource = RevisionOportunidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
