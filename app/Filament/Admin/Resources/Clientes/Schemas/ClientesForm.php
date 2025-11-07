<?php

namespace App\Filament\Admin\Resources\Clientes\Schemas;

use Filament\Forms\Components\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\DateTimePicker;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class ClientesForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sección principal del cliente
                Section::make('Información del Cliente')
                    ->description('Datos principales y configuración de la cuenta')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('nombre')
                            ->label('Nombre del Cliente')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('Ej: Empresa XYZ S.A. de C.V.')
                            ->helperText('Razón social o nombre completo del cliente'),

                        TextInput::make('cuenta')
                            ->label('Número de Cuenta')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: 555555555')
                            ->helperText('Código único de identificación del cliente'),

                        TextInput::make('id_cliente')
                            ->label('ID del Cliente')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->numeric()
                            ->minValue(1)
                            ->placeholder('Ej: 123456')
                            ->helperText('Identificador numérico único en el sistema')

                    ]),

                // Sección de información de contacto mejorada
                Section::make('Información de Contacto')
                    ->description('Datos de contacto y representación legal')
                    ->iconColor('success')
                    ->collapsible()
                    ->columns(2)
                    ->schema([
                        TextInput::make('representante')
                            ->label('Representante Legal')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull()
                            ->placeholder('Ej: Juan Pérez González')
                            ->helperText('Nombre completo de la persona autorizada')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('representante', ucwords(strtolower($state ?? '')))
                            ),

                        TextInput::make('telefono')
                            ->label('Teléfono Principal')
                            ->required()
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Ej: +52 55 1234 5678')
                            ->helperText('Número principal de contacto')
                            ->mask('(999) 999-9999'),

                        TextInput::make('telefono_secundario')
                            ->label('Teléfono Secundario')
                            ->tel()
                            ->maxLength(20)
                            ->placeholder('Ej: +52 55 8765 4321')
                            ->helperText('Número alternativo (opcional)')
                            ->mask('(999) 999-9999'),

                        TextInput::make('correo')
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->placeholder('Ej: contacto@empresa.com')
                            ->helperText('Email principal de comunicación')
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) =>
                                $set('correo', strtolower($state ?? ''))
                            ),

                        TextInput::make('rfc')
                            ->label('RFC')
                            ->required()
                            ->maxLength(13)
                            ->minLength(12)
                            ->placeholder('Ej: ABC123456789')
                            ->helperText('Registro Federal de Contribuyentes')

                    ]),

                // Sección de direcciones completamente rediseñada
                Section::make('Direcciones')
                    ->description('Direcciones fiscales, de entrega y preferencias')
                    ->iconColor('warning')
                    ->collapsible()
                    ->columns(1)
                    ->schema([
                        Group::make([
                            Textarea::make('direccion_fiscal')
                                ->label('Dirección Fiscal')
                                ->required()
                                ->maxLength(500)
                                ->rows(3)
                                ->placeholder('Calle, número, colonia, ciudad, estado, CP')
                                ->helperText('Domicilio fiscal registrado ante el SAT')
                                ->columnSpanFull(),

                            Toggle::make('misma_direccion_entrega')
                                ->label('La dirección de entrega es la misma que la fiscal')
                                ->live()
                                ->columnSpanFull()
                                ->afterStateUpdated(function ($state, $set, $get) {
                                    if ($state) {
                                        $set('direccion_entrega', $get('direccion_fiscal'));
                                    }
                                }),

                            Textarea::make('direccion_entrega')
                                ->label('Dirección de Entrega')
                                ->maxLength(500)
                                ->rows(3)
                                ->placeholder('Dirección donde se realizarán las entregas')
                                ->helperText('Domicilio para entregas de productos/servicios')
                                ->hidden(fn ($get) => $get('misma_direccion_entrega'))
                                ->required(fn ($get) => !$get('misma_direccion_entrega'))
                                ->columnSpanFull(),
                        ])->columnSpanFull(),

                        Group::make([
                            Select::make('direccion_preferencia')
                                ->label('Dirección Preferida para Comunicaciones')
                                ->required()
                                ->options([
                                    'fiscal' => '🏢 Dirección Fiscal',
                                    'entrega' => '🚚 Dirección de Entrega',
                                    'otra' => '📍 Otra Dirección',
                                ])
                                ->default('fiscal')
                                ->live()
                                ->helperText('Dirección principal para envío de documentos')
                                ->columnSpan(1),

                            Textarea::make('otra_direccion')
                                ->label('Especificar Otra Dirección')
                                ->maxLength(500)
                                ->rows(3)
                                ->placeholder('Ingrese la dirección alternativa')
                                ->helperText('Complete este campo si seleccionó "Otra Dirección"')
                                ->visible(fn ($get) => $get('direccion_preferencia') === 'otra')
                                ->required(fn ($get) => $get('direccion_preferencia') === 'otra')
                                ->columnSpan(1),
                        ])->columns(2),
                    ]),

            ]);
    }
}
