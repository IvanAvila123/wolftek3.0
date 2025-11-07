<?php

namespace App\Filament\Admin\Resources\Oportunidads\RelationManagers;

use App\Filament\Admin\Resources\Oportunidads\OportunidadResource;
use App\Models\Linea;
use App\Models\Oportunidad;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Support\Icons\Heroicon;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\HtmlString;

class LineasOportunidadRelationManager extends RelationManager
{
    protected static string $relationship = 'oportunidadLinea';
    protected static ?string $title = 'Líneas de la Oportunidad';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('plan_anterior')->label('Plan Anterior')->disabled(),
            Select::make('plan_nuevo')
                ->label('Plan Nuevo')
                ->options($this->getPlanesOptions())
                ->searchable()
                ->required(),
            TextInput::make('equipo_nuevo')->label('Equipo Nuevo')->required(),
            TextInput::make('sku')->label('SKU')->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('linea.dn')
                    ->label('DN')
                    ->default('Nueva línea')
                    ->formatStateUsing(
                        fn($record) =>
                        !$record->linea_id ? '🆕 Nueva línea' : ($record->linea->dn ?? 'Sin DN')
                    ),

                Tables\Columns\TextColumn::make('linea.fecha_fin')
                    ->label('Fin de Contrato')
                    ->date('d/m/Y')
                    ->visible(fn(?Model $record): bool => $record !== null && $record->exists),

                Tables\Columns\TextColumn::make('plan_nuevo')
                    ->label('Plan Nuevo')
                    ->searchable(),

                Tables\Columns\TextColumn::make('equipo_nuevo')
                    ->label('Equipo'),

                Tables\Columns\BadgeColumn::make('tipo')
                    ->label('Tipo')
                    ->getStateUsing(
                        fn($record) =>
                        !$record->linea_id ? 'Nueva' : $this->getTipoLinea($record->linea->fecha_fin ?? null)
                    )
                    ->colors([
                        'danger' => 'Vencida',
                        'warning' => 'T1',
                        'info' => 'T2',
                        'success' => fn($state) => in_array($state, ['T3', 'Nueva']),
                    ]),
            ])
            ->headerActions([
                $this->getAsignarAction(),
                $this->lineasOcupadas(),
            ])
            ->actions([
                Action::make('editar')
                    ->label('Editar')
                    ->visible(function (): bool {
                        $oportunidad = $this->getOwnerRecord();
                        return !in_array(strtolower($oportunidad->estado), ['aprobada', 'cancelada', 'orden_entregada']);
                    })
                    ->icon('heroicon-o-pencil')
                    ->color('gray') // Un color estándar para la acción de editar

                    // ESTA ES LA LÍNEA CLAVE:
                    // Rellena el formulario con los datos del registro actual.
                    ->fillForm(fn(Model $record): array => $record->toArray())

                    // Reutiliza el mismo formulario que definiste arriba en la clase.
                    ->form(fn(Schema $schema): Schema => $this->form($schema))

                    // La lógica que se ejecuta al guardar los cambios.
                    ->action(function (Model $record, array $data): void {
                        $record->update($data);

                        // Es una buena práctica añadir una notificación de éxito.
                        Notification::make()
                            ->title('Línea guardada correctamente')
                            ->success()
                            ->send();
                    }),
                Action::make('eliminar')
                    ->label('Eliminar')
                    ->icon('heroicon-o-trash')
                    ->visible(function (): bool {
                        $oportunidad = $this->getOwnerRecord();
                        return !in_array(strtolower($oportunidad->estado), ['aprobada', 'cancelada', 'orden_entregada']);
                    })
                    ->action(fn($record) => $record->delete())
                    ->requiresConfirmation(true)
                    ->color('danger'),
            ])
            ->emptyStateHeading('Sin líneas asociadas')
            ->emptyStateDescription('Haz clic en el botón para agregar líneas');
    }

    protected function getAsignarAction(): Action
    {
        return Action::make('asignar')
            ->label(fn($livewire) => $this->esNuevaVenta($livewire) ? 'Agregar Nuevas Líneas' : 'Asignar Líneas')
            ->icon('heroicon-o-plus-circle')
            ->visible(fn(): bool => auth()->user()->can('accion_asignar_linea') && !in_array(strtolower($this->getOwnerRecord()->estado), ['aprobada', 'cancelada', 'orden_entregada']))
            ->modalWidth(fn($livewire) => $this->esNuevaVenta($livewire) ? '3xl' : '5xl')
            ->modalHeading(fn($livewire) => $this->getTituloModal($livewire))
            ->form(fn($livewire) => $this->getFormulario($livewire))
            ->action(fn($data, $livewire) => $this->procesarAsignacion($data, $livewire));
    }

    protected function lineasOcupadas(): Action
    {
        return Action::make('lineas_atrapadas')
            ->label('Líneas atrapadas')
            ->icon('heroicon-o-eye')
            ->visible(fn($livewire) => $this->countLineasAtrapadas($livewire->getOwnerRecord()) > 0)
            ->badge(fn($livewire) => $this->countLineasAtrapadas($livewire->getOwnerRecord()))
            ->badgeColor('warning')
            ->modalWidth('5xl')
            ->modalHeading(fn($livewire) => 'Líneas atrapadas de ' . optional($livewire->getOwnerRecord()->cliente)->nombre)
            ->modalSubmitAction(false) // solo informativo
            ->form([
                Placeholder::make('tabla_atrapadas')
                    ->label('')
                    ->content(function ($livewire) {
                        /** @var Oportunidad $op */
                        $op = $livewire->getOwnerRecord();

                        $lineas = $this->getLineasAtrapadas($op);
                        if ($lineas->isEmpty()) {
                            return 'No hay líneas atrapadas.';
                        }

                        $rows = $lineas->map(function (Linea $linea) {
                            // último registro de ocupación
                            $pivot = $linea->oportunidadLinea()->latest('created_at')->first(); // seguro: no usa sortByDesc sobre null
                            if (! $pivot) {
                                return '';
                            }

                            $opp     = $pivot->oportunidad;
                            $url     = OportunidadResource::getUrl('edit', ['record' => $opp]);
                            $estado  = e($this->estadoLinea($linea));
                            $vence   = optional($linea->fecha_fin)?->format('Y-m-d');
                            $usuario = optional($opp->usuario)->name;

                            return "<tr>
                            <td class='p-2'>" . e($linea->dn) . "</td>
                            <td class='p-2'>" . e($linea->plan) . "</td>
                            <td class='p-2'>{$estado}</td>
                            <td class='p-2'>{$vence}</td>
                            <td class='p-2'><a href='{$url}' target='_blank' class='underline'>Oportunidad #" . e($opp->id) . "</a></td>
                            <td class='p-2'>" . e($usuario) . "</td>
                            <td class='p-2'>" . e(optional($pivot->created_at)?->diffForHumans()) . "</td>
                        </tr>";
                        })->filter()->implode('');

                        $html = <<<HTML
<div style="max-height:420px;overflow:auto">
<table class="fi-ta-table w-full text-sm">
    <thead>
        <tr>
            <th class="text-left p-2">DN</th>
            <th class="text-left p-2">Plan</th>
            <th class="text-left p-2">Estado</th>
            <th class="text-left p-2">Vence</th>
            <th class="text-left p-2">Ubicada en</th>
            <th class="text-left p-2">Usuario</th>
            <th class="text-left p-2">Asignada</th>
        </tr>
    </thead>
    <tbody>{$rows}</tbody>
</table>
</div>
HTML;

                        return new HtmlString($html);
                    }),
            ])
            ->button();
    }


    protected function getFormulario($livewire): array
    {
        $oportunidad = $livewire->getOwnerRecord();

        if ($this->esNuevaVenta($livewire)) {
            return $this->wizardNuevasLineas();
        }

        if (!$oportunidad->cliente) {
            return [Placeholder::make('error')->content('No hay cliente asignado')];
        }

        return $this->wizardRenovacion($oportunidad);
    }

    protected function wizardNuevasLineas(): array
    {
        return [
            Wizard::make([
                Step::make('Cantidad')
                    ->icon(Heroicon::Plus)
                    ->schema([
                        TextInput::make('cantidad_lineas')
                            ->label('Número de líneas')
                            ->numeric()
                            ->minValue(1)
                            ->default(1)
                            ->required(),
                    ]),

                Step::make('Configurar')
                    ->icon(Heroicon::WrenchScrewdriver)
                    ->schema([
                        Select::make('plan_nuevo')
                            ->label('Plan')
                            ->options($this->getPlanesOptions())
                            ->searchable()
                            ->required(),

                        TextInput::make('equipo_nuevo')
                            ->label('Equipo')
                            ->required(),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required(),
                    ])->columns(3),
            ])
                ->skippable(false)
                ->submitAction(new HtmlString(
                    '<button type="submit" class="fi-btn fi-btn-size-sm">Guardar</button>'
                )),
        ];
    }


    protected function wizardRenovacion(Oportunidad $oportunidad): array
    {
        $asignadas = $oportunidad->oportunidadLinea()->pluck('linea_id');

        return [
            Wizard::make([
                Step::make('Seleccionar')
                    ->icon(Heroicon::MagnifyingGlass)
                    ->description('Busca y selecciona DN a renovar')
                    ->schema([
                        Select::make('lineas_ids')
                            ->label('Líneas del cliente')
                            ->multiple()
                            ->searchable()
                            ->preload() // ← mostrará lo que definas en ->options()
                            ->options(function () use ($oportunidad, $asignadas) {
                                return $oportunidad->cliente?->lineas()
                                    ->whereNotIn('id', $asignadas)
                                    ->orderByDesc('fecha_fin')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($l) => [$l->id => $this->formatLineaOption($l)])
                                    ->toArray();
                            })
                            ->getSearchResultsUsing(function (string $search) use ($oportunidad, $asignadas) {
                                return $oportunidad->cliente?->lineas()
                                    ->whereNotIn('id', $asignadas)
                                    ->where(function ($q) use ($search) {
                                        $q->where('dn', 'like', "%{$search}%")
                                            ->orWhere('plan', 'like', "%{$search}%");
                                    })
                                    ->orderByDesc('fecha_fin')
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn($l) => [$l->id => $this->formatLineaOption($l)])
                                    ->toArray();
                            })
                            ->getOptionLabelsUsing(function (array $values) {
                                return Linea::whereIn('id', $values)->get()
                                    ->mapWithKeys(fn($l) => [$l->id => $this->formatLineaOption($l)])
                                    ->toArray();
                            })

                            ->required(),

                        Placeholder::make('seleccionadas')
                            ->content(fn(callable $get) => 'Seleccionadas: ' . count($get('lineas_ids') ?? [])),
                    ]),

                Step::make('Configurar')
                    ->icon(Heroicon::WrenchScrewdriver)
                    ->schema([
                        Select::make('plan_nuevo')
                            ->label('Plan')
                            ->options($this->getPlanesOptions())
                            ->searchable()
                            ->required(),

                        TextInput::make('equipo_nuevo')
                            ->label('Equipo')
                            ->required(),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required(),
                    ])->columns(3),

                // (Opcional) Resumen final:
                // Step::make('Confirmar')
                //     ->icon(Heroicon::HandThumbUp)
                //     ->schema([
                //         Placeholder::make('resumen')
                //             ->content(fn (callable $get) => $this->resumenSeleccion($get('lineas_ids'))),
                // ]),
            ])
                ->skippable(false)
                ->submitAction(new HtmlString(
                    '<button type="submit" class="fi-btn fi-btn-size-sm">Guardar</button>'
                )),
        ];
    }


    protected function procesarAsignacion($data, $livewire): void
    {
        $oportunidad = $livewire->getOwnerRecord();

        try {
            if ($this->esNuevaVenta($livewire)) {
                // Crear líneas nuevas
                $cantidad = intval($data['cantidad_lineas'] ?? 1);

                for ($i = 0; $i < $cantidad; $i++) {
                    $oportunidad->oportunidadLinea()->create([
                        'linea_id' => null,
                        'plan_nuevo' => $data['plan_nuevo'],
                        'equipo_nuevo' => $data['equipo_nuevo'],
                        'sku' => $data['sku'],
                        'fecha_linea' => now(),
                    ]);
                }

                $mensaje = $cantidad == 1 ? "1 línea creada" : "{$cantidad} líneas creadas";
            } else {
                // Asignar líneas existentes
                $lineas = Linea::find($data['lineas_ids'] ?? []);
                $contador = 0;

                foreach ($lineas as $linea) {
                    $oportunidad->oportunidadLinea()->create([
                        'linea_id' => $linea->id,
                        'fecha_linea' => now(),
                        'plan_anterior' => $linea->plan,
                        'plan_nuevo' => $data['plan_nuevo'],
                        'equipo_nuevo' => $data['equipo_nuevo'],
                        'sku' => $data['sku'],
                    ]);
                    $contador++;
                }

                $mensaje = "{$contador} línea(s) asignadas";
            }

            Notification::make()
                ->success()
                ->title('Operación exitosa')
                ->body($mensaje)
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error')
                ->body($e->getMessage())
                ->send();
        }
    }

    // Métodos auxiliares
    protected function esNuevaVenta($livewire): bool
    {
        $tipo = $livewire->getOwnerRecord()->tipo_venta;
        return in_array($tipo, ['venta_nueva', 'adicion']);
    }

    protected function getTituloModal($livewire): string
    {
        $tipo = $livewire->getOwnerRecord()->tipo_venta;
        return match ($tipo) {
            'venta_nueva' => 'Crear Líneas - Venta Nueva',
            'adicion' => 'Agregar Líneas Adicionales',
            default => 'Asignar Líneas para Renovación'
        };
    }

    protected function getTipoLinea($fechaFin): string
    {
        if (!$fechaFin) return 'Sin fecha';

        $dias = Carbon::today()->diffInDays($fechaFin, false);

        return match (true) {
            $dias < 0 => 'Vencida',
            $dias <= 30 => 'T1',
            $dias <= 60 => 'T2',
            $dias <= 90 => 'T3',
            default => 'Fuera de rango'
        };
    }

    protected function getLineasDisponibles($oportunidad)
    {
        $asignadas = $oportunidad->oportunidadLinea()->pluck('linea_id');

        return $oportunidad->cliente->lineas()
            ->whereNotIn('id', $asignadas)
            ->whereNotNull('fecha_fin')
            ->get();
    }

    protected function formatLineaOption(Linea $linea): string
    {
        $tipo = $this->getTipoLinea($linea->fecha_fin);
        $icono = match ($tipo) {
            'Vencida' => '🔴',
            'T1'      => '🟡',
            'T2'      => '🟠',
            'T3'      => '🟢',
            default   => '⚪',
        };

        $dias = $linea->fecha_fin ? \Illuminate\Support\Carbon::today()->diffInDays($linea->fecha_fin, false) : null;
        $estado = $dias !== null
            ? ($dias < 0 ? "vencida hace " . abs($dias) . " días" : "vence en {$dias} días")
            : "sin fecha";

        return "{$icono} {$linea->dn} - {$linea->plan} ({$estado})";
    }


    protected function getPlanesOptions(): array
    {
        $planes = config('LineasOportunidad.planes_empresariales', []);
        return array_combine($planes, $planes);
    }

    protected function countLineasOcupadas(Oportunidad $op): int
    {
        return $this->getLineasOcupadas($op)->count();
    }

    protected function getLineasOcupadas(Oportunidad $op)
    {
        // "Ocupada" = tiene registro en oportunidad_lineas distinto a la oportunidad actual
        return Linea::query()
            ->where('cliente_id', $op->cliente_id)
            ->whereHas('oportunidadLinea', function ($q) use ($op) {
                $q->where('oportunidad_id', '!=', $op->id)
                    ->whereNull('deleted_at'); // quita si tu pivot no tiene soft deletes
            })
            ->with([
                'oportunidadLinea' => fn($q) => $q->latest(),
                'oportunidadLinea.oportunidad.usuario',
            ])
            ->orderBy('dn')
            ->get();
    }

    protected function getLineasAtrapadas(Oportunidad $op)
    {
        // "Atrapada" = la línea está en otra oportunidad distinta a la actual
        return Linea::query()
            ->where('cliente_id', $op->cliente_id)
            ->whereHas('oportunidadLinea', function ($q) use ($op) {
                $q->where('oportunidad_id', '!=', $op->id);
                // ->whereNull('deleted_at'); // descomenta si tu pivot usa soft deletes
            })
            ->with([
                'oportunidadLinea' => fn($q) => $q->latest(),
                'oportunidadLinea.oportunidad.usuario',
            ])
            ->orderBy('dn')
            ->get();
    }

    protected function countLineasAtrapadas(Oportunidad $op): int
    {
        return Linea::query()
            ->where('cliente_id', $op->cliente_id)
            ->whereHas('oportunidadLinea', function ($q) use ($op) {
                $q->where('oportunidad_id', '!=', $op->id);
            })
            ->count();
    }

    protected function estadoLinea(Linea $l): string
    {
        $dias = $l->fecha_fin ? now()->diffInDays($l->fecha_fin, false) : null;

        return match (true) {
            $dias === null  => 'Sin fecha',
            $dias < 0       => 'Vencida',
            $dias <= 30     => 'T1 (≤30d)',
            $dias <= 60     => 'T2 (31–60d)',
            $dias <= 90     => 'T3 (61–90d)',
            default         => 'Futura',
        };
    }

    public static function getBadge(Model $ownerRecord, string $pageClass): ?string
    {
        $count = $ownerRecord->oportunidadLinea()->count();
        return $count > 0 ? (string)$count : null;
    }
}
