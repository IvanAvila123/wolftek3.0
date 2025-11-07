<?php

namespace App\Filament\Admin\Resources\Oportunidads\Pages;

use App\Filament\Admin\Resources\Oportunidads\OportunidadResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOportunidad extends CreateRecord
{
    protected static string $resource = OportunidadResource::class;

    protected static ?string $title = 'Crear Nueva Oportunidad';
}
