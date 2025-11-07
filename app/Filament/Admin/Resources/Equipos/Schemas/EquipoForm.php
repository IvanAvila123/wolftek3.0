<?php

namespace App\Filament\Admin\Resources\Equipos\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class EquipoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informacion del Equipo')
                    ->columns(2)
                    ->components([
                        FileUpload::make('foto_principal')
                        ->label('Foto del Equipo')
                        ->image()
                        ->disk('public') // Asegúrate de tener configurado el disco público
                        ->directory('equipos')
                        ->imageEditor()
                        ->columnSpanFull(),
                        TextInput::make('nombre')
                            ->label('Nombre del Equipo')
                            ->required(),
                        TextInput::make('capacidad')
                            ->label('Capacidad')
                            ->suffix('GB')
                            ->required(),
                        TextInput::make('pantalla')
                            ->label('Pantalla')
                            ->required(),
                        TextInput::make('resolucion')
                            ->label('Resolución')
                            ->required(),
                        TextInput::make('camara')
                            ->label('Cámara')
                            ->required(),
                        TextInput::make('sistema_operativo')
                            ->label('Sistema Operativo')
                            ->required(),
                    ]),
            ]);
    }
}
