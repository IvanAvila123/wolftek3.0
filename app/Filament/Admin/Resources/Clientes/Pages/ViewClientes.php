<?php

namespace App\Filament\Admin\Resources\Clientes\Pages;

use App\Filament\Admin\Resources\Clientes\ClientesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClientes extends ViewRecord
{
    protected static string $resource = ClientesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make()
        ];
    }
}
