<?php

namespace App\Filament\Admin\Resources\Lineas\Schemas;

use Dom\Text;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LineasInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información de la Línea')
                    ->description('Detalles de la línea telefónica')
                    ->icon('heroicon-o-phone')
                    ->iconColor('primary')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('dn')
                            ->label('Número de Teléfono')
                            ->icon('heroicon-o-phone')
                            ->weight('bold')
                            ->size('lg')
                            ->color('primary')
                            ->columnSpanFull()
                            ->copyable()
                            ->copyMessage('¡Número copiado!'),

                        TextEntry::make('plan')
                            ->label('Plan Tarifario')
                            ->icon('heroicon-o-document-text')
                            ->badge()
                            ->color('success')
                            ->size('md')
                            ->copyable()
                            ->copyMessage('¡Plan copiado!')
                            ->placeholder('Sin plan asignado'),

                        TextEntry::make('fecha_fin')
                            ->label('Fecha Fin')
                            ->date('d/m/Y')
                            ->icon('heroicon-o-calendar')
                            ->size('md')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->copyMessage('¡Fecha copiada!')
                            ->placeholder('Sin fecha asignada'),

                        TextEntry::make('equipo')
                            ->label('Equipo Asignado')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->size('md')
                            ->badge()
                            ->color('warning')
                            ->copyable()
                            ->copyMessage('¡Equipo copiado!')
                            ->placeholder('Sin equipo asignado'),

                        TextEntry::make('cliente.nombre')
                            ->label('Cliente Pertinente')
                            ->icon('heroicon-o-user')
                            ->size('md')
                            ->badge()
                            ->color('secondary')
                            ->copyable()
                            ->copyMessage('¡Cliente copiado!')
                            ->placeholder('Sin cliente asignado')
                            ->live(onBlur: true),
                    ]),

                    Section::make('Auditoría')
                    ->description('Información de creación y actualización')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('gray')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Creado')
                            ->icon('heroicon-o-plus-circle')
                            ->size('md')
                            ->color('success')
                            ->copyable()
                            ->copyMessage('¡Fecha copiada!')
                            ->placeholder('Sin fecha de creación')
                            ->dateTime('d/m/Y H:i')
                            ->live(onBlur: true),
                        TextEntry::make('updated_at')
                            ->label('Actualizado')
                            ->icon('heroicon-o-pencil-square')
                            ->size('md')
                            ->color('warning')
                            ->copyable()
                            ->copyMessage('¡Fecha copiada!')
                            ->placeholder('Sin fecha de actualización')
                            ->dateTime('d/m/Y H:i')
                            ->live(onBlur: true),
                        TextEntry::make('deleted_at')
                            ->label('Eliminado')
                            ->icon('heroicon-o-trash')
                            ->size('md')
                            ->color('danger')
                            ->copyable()
                            ->copyMessage('¡Fecha copiada!')
                            ->placeholder('No eliminado')
                            ->dateTime('d/m/Y H:i')
                            ->live(onBlur: true),
                    ]),
            ]);
    }
}
