<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClientesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientes extends ListRecords
{
    protected static string $resource = ClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
