<?php

namespace App\Filament\Admin\Resources\Oportunidads\Pages;

use App\Filament\Admin\Resources\Oportunidads\OportunidadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOportunidad extends ViewRecord
{
    protected static string $resource = OportunidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
            ->visible(fn (): bool => !in_array(strtolower($this->record->estado), ['aprobada', 'cancelada', 'orden_entregada'])),
            DeleteAction::make()
            ->visible(fn (): bool => !in_array(strtolower($this->record->estado), ['aprobada', 'cancelada', 'orden_entregada'])),
        ];
    }

    public function getTitle(): string
    {
        return "Oportunidad #{$this->record->id} - {$this->record->cliente->nombre}";
    }
}
