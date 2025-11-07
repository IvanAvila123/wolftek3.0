<?php

namespace App\Filament\Admin\Resources\Lineas\Pages;

use App\Filament\Admin\Resources\Lineas\LineasResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLineas extends ListRecords
{
    protected static string $resource = LineasResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
