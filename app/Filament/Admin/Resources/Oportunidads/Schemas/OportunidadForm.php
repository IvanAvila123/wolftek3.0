<?php

namespace App\Filament\Admin\Resources\Oportunidads\Schemas;

use App\Models\Oportunidad;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

use function Livewire\Volt\updated;

class OportunidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles de la Oportunidad')
                    ->description('Información y configuración de la oportunidad')
                    ->icon('heroicon-o-briefcase')
                    ->iconColor('primary')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(3)
                    ->schema([
                        Select::make('cliente_id')
                            ->label('Cliente')
                            ->relationship('cliente', 'nombre')
                            ->getOptionLabelFromRecordUsing(fn($record) => "{$record->nombre} - {$record->cuenta}")
                            ->searchable()
                            ->preload()
                            ->placeholder('Selecciona un cliente')
                            ->required(),
                        Select::make('usuario_id')
                            ->label('Usuario')
                            ->relationship('usuario', 'name') // Cambiado de 'users' a 'usuario' y de 'name' a lo que corresponda
                            ->default(fn() => auth()->id())
                            ->disabled()
                            ->dehydrated()
                            ->searchable()
                            ->preload()
                            ->placeholder('Selecciona un usuario')
                            ->required(),
                    ]),

                Section::make('Fechas Importantes')
                    ->description('Fechas clave relacionadas con la oportunidad')
                    ->icon('heroicon-o-calendar')
                    ->iconColor('secondary')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(3)
                    ->schema([
                        DatePicker::make('fecha_captura')
                            ->label('Fecha de Captura')
                            ->default(now())
                            ->required()
                            ->placeholder('Selecciona la fecha de captura')
                            ->helperText('Fecha en que se capturó la oportunidad'),
                        DatePicker::make('fecha_entrega')
                            ->label('Fecha de Entrega')
                            ->visible(fn(): bool => auth()->user()->can('view_all_oportunidades'))
                            ->placeholder('Selecciona la fecha de entrega')
                            ->helperText('Fecha que fue entregada si aplica'),
                        DatePicker::make('fecha_cancelacion')
                            ->label('Fecha de Cancelación')
                            ->visible(fn(): bool => auth()->user()->can('view_all_oportunidades'))
                            ->placeholder('Selecciona la fecha de cancelación')
                            ->helperText('Fecha en que se canceló la oportunidad, si aplica'),
                    ]),
                Section::make('Estado y Tipo de Venta')
                    ->description('Configuración del estado y tipo de venta')
                    ->icon('heroicon-o-bars-arrow-down')
                    ->iconColor('success')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(2)
                    ->schema([
                        Select::make('estado')
                            ->label('Estado de la Oportunidad')
                            ->options(fn() => config('oportunidad.estados'))
                            ->visible(fn(): bool => auth()->user()->can('view_all_oportunidades'))
                            ->disabled(fn(callable $get) => in_array($get('estado'), config('oportunidad.estados_no_editables', [])))
                            ->required()
                            ->placeholder('Selecciona el estado de la oportunidad')
                            ->helperText('Estado actual de la oportunidad'),
                        Select::make('tipo_venta')
                            ->label('Tipo de Venta')
                            ->options(fn() => config('oportunidad.tipos_venta'))
                            ->required()
                            ->placeholder('Selecciona el tipo de venta')
                            ->helperText('Tipo de venta asociado a la oportunidad'),
                    ]),
                Section::make('Notas Adicionales y direccion')
                    ->description('Información adicional relevante y direccion de entrega')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('warning')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(1)
                    ->schema([
                        Textarea::make('notas')
                            ->label('Notas')
                            ->maxLength(500)
                            ->autosize()
                            ->placeholder('Agrega cualquier nota adicional relevante')
                            ->helperText('Notas adicionales sobre la oportunidad, si las hay'),
                        Textarea::make('direccion_entrega')
                            ->maxLength(255)
                            ->required()
                            ->placeholder('Direccion que se entregara los equipos')
                            ->helperText('Si no se coloca ninguna direccion se ocupara el de cibeles')
                            ->default('CALLE PLAZA VILLA MADRID 8 ROMA NORTE CUAUHTEMOC CDMX CP 06700')
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
