<?php

namespace App\Filament\Admin\Resources\Oportunidads\RelationManagers;

use App\Filament\Admin\Resources\Oportunidads\OportunidadResource;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use PhpParser\Node\Stmt\Label;

class RevisionOportunidadRelationManager extends RelationManager
{
    protected static string $relationship = 'revisionOportunidad';
    protected static ?string $title = 'Revisiones';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('view_oportunidad_revisiones_relation');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('usuario.name')->label('Usuario'),
                TextColumn::make('estado')->label('Estado'),
                TextColumn::make('fecha_aprobacion')
                    ->label('Fecha Aprobación')
                    ->dateTime('d/m/Y H:i'),
                TextColumn::make('fecha_rechazo')
                    ->label('Fecha Rechazo')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->label('Actualizado')->dateTime('d/m/Y H:i')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->headerActions([
                Action::make('nuevo_rechazo')
                    ->label('Nuevo Rechazo')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(): bool => auth()->user()->can('crear_rechazo_oportunidad'))
                    ->modalHeading('Registrar Rechazo')
                    ->modalDescription('Documenta el motivo del rechazo y adjunta imágenes de referencia')
                    ->modalWidth('3xl')
                    ->form([
                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'rechazada' => 'Rechazado',
                            ])
                            ->default('rechazada')
                            ->disabled()
                            ->dehydrated(),

                        Textarea::make('comentarios')
                            ->label('Motivo del Rechazo')
                            ->placeholder('Explica detalladamente por qué se rechaza...')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        FileUpload::make('imagenes_rechazo')
                            ->label('Capturas de Pantalla')
                            ->image()
                            ->multiple()
                            ->maxFiles(5)
                            ->disk('public')
                            ->directory('rechazos')
                            ->imageEditor()
                            ->helperText('Adjunta imágenes mostrando los errores encontrados')
                            ->columnSpanFull(),

                        DateTimePicker::make('fecha_rechazo')
                            ->label('Fecha de Rechazo')
                            ->default(now())
                            ->disabled()
                            ->dehydrated(),
                    ])
                    ->action(function (array $data, RelationManager $livewire): void {
                        try {
                            // Crear el rechazo
                            $livewire->getOwnerRecord()
                                ->revisionOportunidad()
                                ->create([
                                    'usuario_id' => auth()->id(),
                                    'estado' => 'rechazada',
                                    'comentarios' => $data['comentarios'],
                                    'imagenes_rechazo' => $data['imagenes_rechazo'],
                                    'fecha_rechazo' => now(),
                                ]);

                            Notification::make()
                                ->title('Rechazo registrado')
                                ->body('El rechazo ha sido registrado correctamente')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al registrar')
                                ->body('No se pudo registrar el rechazo: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    })
                    ->modalSubmitActionLabel('Registrar Rechazo')
                    ->modalCancelActionLabel('Cancelar'),
            ])
            ->actions([
                ViewAction::make()
                    ->label('Ver Detalle')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->mountUsing(function ($form, $record) {
                        // Se ejecuta al abrir el modal
                        if ($record->estado === 'rechazada' && $record->visto_por_vendedor == 0) {
                            $record->update([
                                'visto_por_vendedor' => 1,
                                'fecha_visto' => now(),
                            ]);
                        }
                    })
                    ->infolist([
                        // Header con alerta
                        Section::make()
                            ->schema([
                                TextEntry::make('alerta')
                                    ->hiddenLabel()
                                    ->default(fn($record) => $record->estado === 'aprobada' ? 'Esta oportunidad fue ✅ Aprobada' : 'Esta oportunidad fue 🚩Rechazada')
                                    ->color('danger')
                                    ->weight('bold')
                                    ->size('lg'),
                            ])
                            ->columnSpanFull(),

                        // Grid principal
                        Grid::make(2)
                            ->schema([
                                // Columna izquierda
                                Section::make('Detalles del Rechazo')
                                    ->icon('heroicon-o-document-text')
                                    ->schema([
                                        TextEntry::make('estado')
                                            ->label('Estado')
                                            ->badge()
                                            ->color('danger')
                                            ->formatStateUsing(fn($state) => $state === 'aprobada' ? 'Aprobada' : 'Rechazada'),

                                        TextEntry::make('comentarios')
                                            ->label('Motivo')
                                            ->prose()
                                            ->markdown()
                                            ->columnSpanFull(),

                                        TextEntry::make('fecha_aprobacion')
                                            ->label('Fecha de Aprobación')
                                            ->visible(fn($record) => $record->estado === 'aprobada')
                                            ->date('d/m/Y H:i')
                                            ->icon('heroicon-o-calendar'),

                                        TextEntry::make('fecha_rechazo')
                                            ->label('Fecha de Rechazo')
                                            ->visible(fn($record) => $record->estado === 'rechazada')
                                            ->date('d/m/Y H:i')
                                            ->icon('heroicon-o-calendar'),
                                    ]),

                                // Columna derecha
                                Section::make('Información Adicional')
                                    ->icon('heroicon-o-information-circle')
                                    ->schema([
                                        TextEntry::make('oportunidad.cliente.nombre')
                                            ->label('Cliente')
                                            ->weight('bold')
                                            ->copyable()
                                            ->copyMessage('Cliente copiado')
                                            ->icon('heroicon-o-building-office-2'),

                                        TextEntry::make('usuario.name')
                                            ->label('Revisor')
                                            ->icon('heroicon-o-user')
                                            ->badge()
                                            ->color('primary'),

                                        TextEntry::make('created_at')
                                            ->label('Registrado')
                                            ->since()
                                            ->icon('heroicon-o-clock')
                                            ->tooltip(fn($record) => $record->created_at->format('d/m/Y H:i')),
                                    ]),
                            ]),

                        Section::make('Evidencia Fotográfica')
                            ->description('Capturas de pantalla que muestran los errores encontrados')
                            ->icon('heroicon-o-camera')
                            ->iconColor('warning')
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
                            ->collapsible()
                            ->collapsed(),
                    ]),
            ]);
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->revisionOportunidad()
            ->where('estado', 'rechazada')
            ->where('visto_por_vendedor', 0)
            ->count();

        return $count > 0 ? (string) $count : null;
    }


    public static function getBadgeColor(Model $ownerRecord, string $pageClass): ?string
    {
        return 'danger';
    }
}
