<?php

namespace App\Filament\Admin\Resources\Lineas\Schemas;

use Dom\Text;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LineasForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Línea')
                    ->description('Información y configuración de la línea')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(3)
                    ->schema([
                        // Aquí puedes agregar los campos del formulario para la entidad "Línea"
                        TextInput::make('dn')
                            ->label('Número de Línea')
                            ->required()
                            ->placeholder('Ej: +52 55 1234 5678')
                            ->helperText('Número telefónico asignado a la línea')
                            ->live(onBlur: true),
                        DatePicker::make('fecha_fin')
                            ->label('Fecha Fin')
                            ->required()
                            ->helperText('Fecha en que la línea fue activada')
                            ->live(onBlur: true),
                        TextInput::make('plan')
                            ->label('Plan Tarifario')
                            ->required()
                            ->placeholder('Ej: ARMALO ATT 499 CTRL')
                            ->helperText('Tipo de plan contratado para la línea')
                            ->live(onBlur: true),
                        TextInput::make('equipo')
                            ->label('Equipo Asignado')
                            ->required()
                            ->default('Sin equipo asignado')
                            ->placeholder('Ej: iPhone 14 Pro Max')
                            ->helperText('Modelo de equipo asignado a la línea')
                            ->live(onBlur: true),
                        Select::make('cliente_id')
                            ->required()
                            ->relationship('clientes', 'nombre')
                            ->placeholder('Ej: AT&T México S.A. de C.V.')
                            ->helperText('ID del cliente al que pertenece la línea')
                            ->live(onBlur: true),

                    ]),
            ]);
    }
}
