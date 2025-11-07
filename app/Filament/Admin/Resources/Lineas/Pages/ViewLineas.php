<?php

namespace App\Filament\Admin\Resources\Lineas\Pages;

use App\Filament\Admin\Resources\Lineas\LineasResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewLineas extends ViewRecord
{
    protected static string $resource = LineasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
