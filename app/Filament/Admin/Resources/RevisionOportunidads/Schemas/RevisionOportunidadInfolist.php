<?php

namespace App\Filament\Admin\Resources\RevisionOportunidads\Schemas;

use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

class RevisionOportunidadInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Revisión de Oportunidad')
                    ->description('Información detallada sobre la revisión de la oportunidad')
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('oportunidad.cliente.nombre')
                            ->label('Oportunidad')
                            ->formatStateUsing(fn ($state, $record) => "#{$record->oportunidad_id} · {$state}")
                            ->icon('heroicon-o-briefcase')
                            ->weight('bold')
                            ->size('lg')
                            ->color('primary')
                            ->columnSpanFull()
                            ->copyable()
                            ->copyMessage('¡Nombre copiado!')
                            ->placeholder('Sin nombre registrado'),
                        TextEntry::make('usuario.name')
                            ->label('Revisado por')
                            ->icon('heroicon-o-user-circle')
                            ->color('secondary')
                            ->copyable()
                            ->copyMessage('¡Nombre copiado!')
                            ->placeholder('Sin usuario asignado'),
                        TextEntry::make('estado')
                            ->label('Estado de la Revisión')
                            ->icon('heroicon-o-document-check')
                            ->badge()
                            ->color(fn ($state) => match ($state) { 'aprobada' => 'success', 'rechazada' => 'danger', default => 'warning' })
                            ->placeholder('Sin estado asignado'),
                        TextEntry::make('comentarios')
                            ->label('Comentario')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->color('info')
                            ->placeholder('Sin comentario registrado'),
                    ]),
                Section::make('Fechas de la Revisión')
                    ->description('Fechas importantes relacionadas con la revisión')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('primary')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('fecha_aprobacion')
                            ->label('Fecha de Aprobación')
                            ->icon('heroicon-o-check-circle')
                            ->color('success')
                            ->placeholder('No aprobada')
                            ->date(),
                        TextEntry::make('fecha_rechazo')
                            ->label('Fecha de Rechazo')
                            ->icon('heroicon-o-x-circle')
                            ->color('danger')
                            ->placeholder('No rechazada')
                            ->date(),
                    ]),
                Section::make('Información Adicional')
                    ->description('Detalles adicionales sobre la revisión de la oportunidad')
                    ->icon('heroicon-o-information-circle')
                    ->iconColor('secondary')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID de la Revisión')
                            ->icon('heroicon-o-hashtag')
                            ->weight('bold')
                            ->size('md')
                            ->color('info')
                            ->copyable()
                            ->copyMessage('¡ID copiado!')
                            ->placeholder('Sin ID asignado'),
                        TextEntry::make('created_at')
                            ->label('Creado el')
                            ->icon('heroicon-o-calendar')
                            ->color('primary')
                            ->placeholder('Sin fecha de creación')
                            ->date(),
                        TextEntry::make('updated_at')
                            ->label('Última Actualización')
                            ->icon('heroicon-o-calendar')
                            ->color('secondary')
                            ->placeholder('Sin fecha de actualización')
                            ->date(),
                        TextEntry::make('deleted_at')
                            ->label('Eliminado el')
                            ->icon('heroicon-o-calendar-x')
                            ->color('danger')
                            ->placeholder('No eliminado')
                            ->date(),
                    ]),

                    Section::make('Imágenes de Rechazo')
                    ->description('Imágenes proporcionadas al rechazar la revisión')
                    ->icon('heroicon-o-photo')
                    ->iconColor('danger')
                    ->collapsible()
                    ->persistCollapsed()
                    ->columnSpanFull()
                    ->columns(1)
                    ->schema([
                        ImageEntry::make('imagenes_rechazo')
                                    ->label(false)
                                    ->getStateUsing(function ($record) {
                                        return collect($record->imagenes_rechazo ?? [])->map(function ($path) {
                                            $cleanPath = str_replace('\\/', '/', $path);
                                            // Forzar 127.0.0.1 en lugar de localhost
                                            return str_replace('localhost', '127.0.0.1', Storage::disk('public')->url($cleanPath));
                                        })->toArray();
                                    })
                                    ->simpleLightbox()
                                    ->visible(fn($record) => !empty($record->imagenes_rechazo)),
                    ])
            ]);
    }
}
