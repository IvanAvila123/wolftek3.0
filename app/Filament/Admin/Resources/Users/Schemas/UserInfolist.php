<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Dom\Text;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Información del Usuario')
                    ->collapsible()
                    ->compact(false)
                    ->columnSpanFull()
                    ->columns(2)
                    ->description('Información del usuario')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('primary')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nombre del usuario')
                            ->icon('heroicon-o-user')
                            ->iconColor('primary')
                            ->placeholder('N/A'),
                        TextEntry::make('username')
                            ->label('Usuario')
                            ->icon('heroicon-o-at-symbol')
                            ->iconColor('success')
                            ->placeholder('N/A'),
                        TextEntry::make('email')
                            ->label('Correo')
                            ->icon('heroicon-o-envelope')
                            ->iconColor('secondary')
                            ->placeholder('N/A'),
                        TextEntry::make('roles.name')
                            ->label('Rol')
                            ->icon('heroicon-o-shield-check')
                            ->iconColor('warning')
                            ->placeholder('N/A'),
                        TextEntry::make('created_at')
                            ->label('Creado el')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('info')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('N/A'),
                        TextEntry::make('updated_at')
                            ->label('Actualizado el')
                            ->icon('heroicon-o-pencil-square')
                            ->iconColor('warning')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('N/A'),
                        TextEntry::make('deleted_at')
                            ->label('Eliminado el')
                            ->icon('heroicon-o-trash')
                            ->iconColor('danger')
                            ->dateTime('d/m/Y H:i')
                            ->placeholder('N/A'),
                    ]),
            ]);
    }
}
