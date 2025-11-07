<?php

namespace App\Filament\Admin\Resources\RevisionOportunidads\Pages;

use App\Filament\Admin\Resources\RevisionOportunidads\RevisionOportunidadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRevisionOportunidad extends CreateRecord
{
    protected static string $resource = RevisionOportunidadResource::class;

    public function getTitle(): string
    {
        return "Crear Revisión de Oportunidad";
    }


}

