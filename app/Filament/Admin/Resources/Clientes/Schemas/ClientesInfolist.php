<?php

namespace App\Filament\Admin\Resources\Clientes\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ClientesInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // Sección principal del cliente con diseño destacado
                Section::make('Información del Cliente')
                    ->description('Datos principales de la cuenta del cliente')
                    ->icon('heroicon-o-user-circle')
                    ->iconColor('primary')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('nombre')
                            ->label('Nombre del Cliente')
                            ->icon('heroicon-o-building-office-2')
                            ->weight('bold')
                            ->size('lg')
                            ->color('primary')
                            ->columnSpanFull()
                            ->copyable()
                            ->copyMessage('¡Nombre copiado!')
                            ->placeholder('Sin nombre registrado'),

                        TextEntry::make('cuenta')
                            ->label('Número de Cuenta')
                            ->icon('heroicon-o-identification')
                            ->badge()
                            ->color('success')
                            ->copyable()
                            ->copyMessage('¡Cuenta copiada!')
                            ->placeholder('Sin cuenta asignada'),

                        TextEntry::make('id_cliente')
                            ->label('ID del Cliente')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->copyMessage('¡ID copiado!')
                            ->placeholder('Sin ID asignado'),
                    ]),

                // Sección de contacto con mejor organización
                Section::make('Información de Contacto')
                    ->description('Datos de contacto y representación legal')
                    ->icon('heroicon-o-phone')
                    ->iconColor('success')
                    ->collapsible()
                    ->persistCollapsed()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('representante')
                                    ->label('Representante Legal')
                                    ->icon('heroicon-o-user-plus')
                                    ->weight('semibold')
                                    ->copyable()
                                    ->copyMessage('¡Representante copiado!')
                                    ->placeholder('Sin representante asignado')
                                    ->color('gray'),

                                TextEntry::make('rfc')
                                    ->label('RFC')
                                    ->icon('heroicon-o-document-text')
                                    ->copyable()
                                    ->copyMessage('¡RFC copiado!')
                                    ->placeholder('Sin RFC registrado')
                                    ->fontFamily('mono')
                                    ->color('gray'),

                                TextEntry::make('telefono')
                                    ->label('Teléfono de Contacto')
                                    ->icon('heroicon-o-device-phone-mobile')
                                    ->copyable()
                                    ->copyMessage('¡Teléfono copiado!')
                                    ->placeholder('Sin teléfono registrado')
                                    ->color('gray'),

                                TextEntry::make('correo')
                                    ->label('Correo Electrónico')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable()
                                    ->copyMessage('¡Correo copiado!')
                                    ->placeholder('Sin correo registrado')
                                    ->color('gray')
                                    ->url(fn ($record) => $record->correo ? "mailto:{$record->correo}" : null)
                                    ->openUrlInNewTab(),
                            ])
                    ]),

                // Sección de direcciones mejorada
                Section::make('Direcciones')
                    ->description('Direcciones fiscales y de entrega del cliente')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('warning')
                    ->collapsible()
                    ->persistCollapsed()
                    ->schema([
                        Grid::make(1)
                            ->schema([
                                TextEntry::make('direccion_fiscal')
                                    ->label('Dirección Fiscal')
                                    ->icon('heroicon-o-building-office')
                                    ->weight('semibold')
                                    ->copyable()
                                    ->copyMessage('¡Dirección fiscal copiada!')
                                    ->placeholder('Sin dirección fiscal registrada')
                                    ->color('warning')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn (string $state = null): string =>
                                        $state ? ucwords(strtolower($state)) : ''
                                    ),

                                TextEntry::make('direccion_entrega')
                                    ->label('Dirección de Entrega')
                                    ->icon('heroicon-o-truck')
                                    ->weight('semibold')
                                    ->copyable()
                                    ->copyMessage('¡Dirección de entrega copiada!')
                                    ->placeholder('Sin dirección de entrega registrada')
                                    ->color('info')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn (string $state = null): string =>
                                        $state ? ucwords(strtolower($state)) : ''
                                    ),

                                TextEntry::make('direccion_preferencia')
                                    ->label('Dirección de Preferencia')
                                    ->icon('heroicon-o-star')
                                    ->weight('semibold')
                                    ->copyable()
                                    ->copyMessage('¡Dirección de preferencia copiada!')
                                    ->placeholder('Sin dirección de preferencia registrada')
                                    ->color('success')
                                    ->columnSpanFull()
                                    ->formatStateUsing(fn (string $state = null): string =>
                                        $state ? ucwords(strtolower($state)) : ''
                                    ),
                            ])
                    ]),

                // Sección adicional para metadatos (opcional)
                Section::make('Información del Sistema')
                    ->description('Datos técnicos y de auditoría')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->iconColor('gray')
                    ->collapsible()
                    ->collapsed()
                    ->compact()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('created_at')
                                    ->label('Fecha de Creación')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->color('gray')
                                    ->size('sm'),

                                TextEntry::make('updated_at')
                                    ->label('Última Modificación')
                                    ->icon('heroicon-o-calendar')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->color('gray')
                                    ->size('sm'),

                                TextEntry::make('deleted_at')
                                    ->label('Fecha de Eliminación')
                                    ->icon('heroicon-o-trash')
                                    ->dateTime('d/m/Y H:i:s')
                                    ->color('red')
                                    ->size('sm')
                                    ->visible(fn ($record) => $record->trashed()),
                            ])
                    ]),
            ]);
    }
}
