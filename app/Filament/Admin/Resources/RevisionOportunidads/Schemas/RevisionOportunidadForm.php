<?php

namespace App\Filament\Admin\Resources\RevisionOportunidads\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Date;

class RevisionOportunidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Revisión de Oportunidad')
                    ->description('Formulario para la revisión de oportunidades')
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('success')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(1)
                    ->schema([
                       Select::make('oportunidad_id')
                            ->label('Oportunidad')
                            ->relationship('oportunidad', 'id', fn ($query) => $query->with('cliente'))
                            ->getOptionLabelFromRecordUsing(fn ($record) => "#{$record->id} · " . ($record->cliente?->nombre ?? 'Sin cliente'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Selecciona una oportunidad')
                            ->required(),
                       Select::make('usuario_id')
                            ->label('Usuario')
                            ->relationship('usuario', 'name') // Cambiado de 'users' a 'usuario' y de 'name' a lo que corresponda
                            ->default(fn () => auth()->id())
                            ->disabled()
                            ->dehydrated()
                            ->searchable()
                            ->preload()
                            ->placeholder('Selecciona un usuario')
                            ->required(),
                    ]),
                    Section::make('Estado de la Revisión')
                    ->description('Aprobar o rechazar la oportunidad')
                    ->icon('heroicon-o-check-circle')
                    ->iconColor('warning')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(1)
                    ->schema([
                       Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'aprobada' => 'Aprobada',
                                'rechazada' => 'Rechazada',
                            ])
                            ->required()
                            ->placeholder('Selecciona un estado'),
                    ]),
                    Section::make('Comentarios')
                    ->description('Comentarios adicionales sobre la revisión')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->iconColor('info')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(1)
                    ->schema([
                       Textarea::make('comentarios')
                            ->label('Comentario')
                            ->rows(4)
                            ->placeholder('Agrega un comentario sobre la revisión')
                            ->required(),
                    ]),
                    Section::make('Fechas de Revisión')
                    ->description('Fechas importantes relacionadas con la revisión')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('secondary')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(2)
                    ->schema([
                        DatePicker::make('fecha_aprobacion')
                            ->label('Fecha de Aprobación')
                            ->date('d-m-Y')
                            ->placeholder('Selecciona la fecha de aprobación')
                            ->helperText('Fecha en que se aprobó la oportunidad, si aplica'),
                        DatePicker::make('fecha_rechazo')
                            ->label('Fecha de Rechazo')
                            ->date('d-m-Y')
                            ->placeholder('Selecciona la fecha de rechazo')
                            ->helperText('Fecha en que se rechazó la oportunidad, si aplica'),
                        DatePicker::make('created_at')
                            ->label('Fecha de Creación')
                            ->disabled()
                            ->dehydrated(false)
                            ->date('d-m-Y H:i:s')
                            ->placeholder('Fecha de creación del registro')
                            ->helperText('Fecha en que se creó el registro'),
                        DatePicker::make('updated_at')
                            ->label('Fecha de Actualización')
                            ->disabled()
                            ->dehydrated(false)
                            ->date('d-m-Y H:i:s')
                            ->placeholder('Fecha de última actualización del registro')
                            ->helperText('Fecha en que se actualizó el registro por última vez'),
                         DatePicker::make('deleted_at')
                            ->label('Fecha de Eliminación')
                            ->disabled()
                            ->dehydrated(false)
                            ->date('d-m-Y H:i:s')
                            ->placeholder('Fecha de eliminación del registro')
                            ->helperText('Fecha en que se eliminó el registro, si aplica'),

                    ]),     // Elimina el espaciado compacto
            ]);
    }
}
