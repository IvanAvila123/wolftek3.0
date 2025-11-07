<?php

namespace App\Filament\Admin\Resources\Oportunidads\Pages;

use App\Filament\Admin\Resources\Oportunidads\OportunidadResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditOportunidad extends EditRecord
{
    protected static string $resource = OportunidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make()
            ->visible(fn (): bool => !in_array(strtolower($this->record->estado), ['aprobada', 'cancelada', 'orden_entregada'])),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
