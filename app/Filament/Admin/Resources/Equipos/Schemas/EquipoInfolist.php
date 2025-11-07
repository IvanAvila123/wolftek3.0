<?php

namespace App\Filament\Admin\Resources\Equipos\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class EquipoInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nombre'),
                TextEntry::make('capacidad'),
                TextEntry::make('pantalla'),
                TextEntry::make('resolucion'),
                TextEntry::make('camara'),
                TextEntry::make('sistema_operativo'),
                TextEntry::make('foto_principal'),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
