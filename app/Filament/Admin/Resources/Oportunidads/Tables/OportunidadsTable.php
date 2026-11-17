<?php

namespace App\Filament\Admin\Resources\Oportunidads\Tables;

use App\Filament\Admin\Resources\Oportunidads\Pages\CreateOportunidad;
use App\Models\Oportunidad;
use App\Models\RevisionOportunidad;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use GuzzleHttp\Promise\Create;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OportunidadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading('Sin Oportunidades')
            ->emptyStateDescription("Favor de crear una nueva oportunidad")
            ->columns([
                TextColumn::make('cliente.nombre')
                    ->label('Cliente')
                    ->sortable(),
                TextColumn::make('usuario.name')
                    ->label('Vendedor')
                    ->sortable(),
                BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'gray' => 'pendiente',
                        'warning' => 'revision',
                        'info' => ['haciendo_contratos', 'entrega_contratos', 'revision_contratos'],
                        'primary' => ['captura', 'verificacion_credito'],
                        'danger' => ['rechazada_credito', 'verificacion_rechazada', 'rechazada', 'cancelada'],
                        'success' => ['verificacion_aprobada', 'orden_entregada', 'aprobada'],
                        'secondary' => ['asignacion_equipo', 'cancela_envios', 'envios_confirmar', 'envios_ruta'],
                    ])
                    ->formatStateUsing(fn(string $state): string => config("oportunidad.estados.{$state}", $state))
                    ->sortable(),

                BadgeColumn::make('tipo_venta')
                    ->label('Tipo de Venta')
                    ->colors([
                        'success' => 'venta_nueva',
                        'warning' => ['renovacion', 'renovacion_anticipada_t1', 'renovacion_anticipada_t2', 'renovacion_anticipada_t3'],
                        'info' => 'adicion',
                    ])
                    ->formatStateUsing(fn(string $state): string => config("oportunidad.tipos_venta.{$state}", $state)),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),

                SelectFilter::make('estado')
                ->options([
                    'orden_entregada' => 'Orden Entregada',
                    'envios_ruta' => 'Envios/en ruta',
                    'pendiente' => 'Pendiente',
                    'cancelada' => 'Cancelada',

                ])
                ->label('Estado'),

                Filter::make('fecha_entrega')
                ->form([
                    DatePicker::make('entregada_desde')
                        ->label('Entregada Desde'),
                    DatePicker::make('entregada_hasta')
                        ->label('Entregada Hasta'),
                ])
                ->query(function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['entregada_desde'],
                            fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega', '>=', $date),
                        )
                        ->when(
                            $data['entregada_hasta'],
                            fn (Builder $query, $date): Builder => $query->whereDate('fecha_entrega', '<=', $date),
                        );
                }),

                SelectFilter::make('tipo_venta')
                    ->label('Tipo de Venta')
                    ->options(config('oportunidad.tipos_venta'))
                    ->multiple(),

                SelectFilter::make('usuario_id')
                    ->label('Usuario')
                    ->relationship('usuario', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('cliente_id')
                    ->label('Cliente')
                    ->relationship('cliente', 'nombre')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make()
                        ->visible(fn(Oportunidad $record): bool => !in_array(strtolower($record->estado), ['aprobada', 'cancelada','orden_entregada'])),

                    Action::make('aprobar')
                        ->label('Aprobar')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Aprobar Oportunidad')
                        ->modalDescription('¿Estás seguro de que quieres aprobar esta oportunidad?')
                        ->visible(function (Oportunidad $record): bool {
                            return auth()->user()->can('aprobar_oportunidad') && !in_array(strtolower($record->estado), ['aprobada', 'cancelada', 'orden_entregada']);
                        })
                        ->schema([
                            Textarea::make('comentarios')
                                ->label('Comentario (opcional)')
                                ->rows(3),
                        ])
                        ->action(function (Oportunidad $record, array $data) {
                            // Cambia el estado y deja historial de quién aprobó y cuándo
                            DB::transaction(function () use ($record, $data) {
                                $record->update(['estado' => 'aprobada']);

                                $record->revisionOportunidad()->create([
                                    'usuario_id' => auth()->id(),
                                    'estado' => 'aprobada',
                                    'comentarios' => $data['comentarios'] ?? null,
                                    'fecha_aprobacion' => now(),
                                ]);
                            });

                            Notification::make()
                                ->title('Oportunidad aprobada')
                                ->success()
                                ->send();
                        }),

                    Action::make('cancelar')
                        ->label('Cancelar')
                        ->icon('heroicon-o-trash')
                        ->color('gray')
                        ->requiresConfirmation()
                        ->modalHeading('Cancelar Oportunidad')
                        ->modalDescription('¿Estás seguro de que quieres cancelar esta oportunidad?')
                        ->visible(fn(Oportunidad $record): bool => !in_array(strtolower($record->estado), ['aprobada', 'cancelada', 'orden_entregada']))
                        ->action(function (Oportunidad $record) {
                            $record->update([
                                'estado' => 'cancelada',
                                'fecha_cancelacion' => now()
                            ]);

                            Notification::make()
                                ->title('Oportunidad cancelada')
                                ->warning()
                                ->send();
                        }),

                    Action::make('reabrir')
                        ->label('Reabrir')
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->requiresConfirmation()
                        ->modalHeading('Reabrir Oportunidad')
                        ->modalDescription('¿Estás seguro de que quieres reabrir esta oportunidad?')
                        ->visible(fn(Oportunidad $record): bool => auth()->user()->can('accion_reabrir_oportunidad') // <-- Asegúrate de tener este permiso creado
                            && in_array(strtolower($record->estado), ['cancelada', 'rechazada']))
                        ->action(function (Oportunidad $record) {
                            $record->update(['estado' => 'pendiente']);

                            Notification::make()
                                ->title('Oportunidad reabierta')
                                ->info()
                                ->send();
                        }),
                ])
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
