<?php

namespace App\Filament\Admin\Resources\Oportunidads\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OportunidadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Oportunidad')
                    ->description('Detalles de la oportunidad seleccionada')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('cliente.nombre')
                            ->label('Cliente')
                            ->icon('heroicon-o-building-office-2')
                            ->weight('bold')
                            ->size('lg')
                            ->color('primary')
                            ->columnSpanFull()
                            ->copyable()
                            ->copyMessage('¡Nombre copiado!')
                            ->placeholder('Sin nombre registrado'),

                        TextEntry::make('cliente.cuenta')
                            ->label('Número de Cuenta')
                            ->icon('heroicon-o-identification')
                            ->badge()
                            ->color('success')
                            ->copyable()
                            ->copyMessage('¡Cuenta copiada!')
                            ->placeholder('Sin cuenta asignada'),

                        TextEntry::make('cliente.id_cliente')
                            ->label('ID del Cliente')
                            ->icon('heroicon-o-hashtag')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->copyMessage('¡ID copiado!')
                            ->placeholder('Sin ID asignado'),

                        TextEntry::make('usuario.name')
                            ->label('Vendedor Asignado')
                            ->icon('heroicon-o-user-circle')
                            ->color('secondary')
                            ->copyable()
                            ->copyMessage('¡Nombre copiado!')
                            ->placeholder('Sin vendedor asignado'),

                        TextEntry::make('estado')
                            ->label('Estado Actual')
                            ->icon('heroicon-o-flag')
                            ->badge()
                            ->color('primary')
                            ->formatStateUsing(fn(string $state): string => config("oportunidad.estados.{$state}", $state))
                            ->placeholder('Sin estado definido'),

                        TextEntry::make('tipo_venta')
                            ->label('Tipo de Venta')
                            ->icon('heroicon-o-currency-dollar')
                            ->badge()
                            ->color('success')
                            ->placeholder('Sin tipo definido'),

                        TextEntry::make('direccion_entrega')
                         ->label('Direccion de entrega')
                         ->icon('heroicon-o-building-office')
                         ->color('secondary')

                    ]),

                Section::make('Fechas Clave')
                    ->description('Fechas importantes relacionadas con la oportunidad')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('secondary')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('fecha_captura')
                            ->label('Fecha de Captura')
                            ->icon('heroicon-o-calendar-days')
                            ->date('d/m/Y')
                            ->color('primary')
                            ->placeholder('Sin fecha de captura'),
                        TextEntry::make('fecha_entrega')
                            ->label('Fecha de Entrega')
                            ->icon('heroicon-o-truck')
                            ->date('d/m/Y')
                            ->color('success')
                            ->placeholder('Sin fecha de entrega'),
                        TextEntry::make('fecha_cancelacion')
                            ->label('Fecha de Cancelación')
                            ->icon('heroicon-o-x-circle')
                            ->date('d/m/Y')
                            ->color('danger')
                            ->placeholder('Sin fecha de cancelación'),
                    ]),
            ]);
    }
}
