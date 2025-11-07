<?php

namespace App\Filament\Admin\Pages;

use App\Livewire\RenovacionesStats;
use App\Models\Cliente;
use App\Models\Linea;
use App\Models\User;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Collection;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Section;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;

class Renovaciones extends Page implements HasTable
{
    use InteractsWithTable;
    use HasPageShield;

    protected string $view = 'filament.admin.pages.renovaciones';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::FolderPlus;

    public Collection $vencidas;
    public Collection $t1;
    public Collection $t2;
    public Collection $t3;

    public function getHeaderWidgets(): array
    {
        return [
            RenovacionesStats::class,
        ];
    }

    // Tab activo
    public string $activeTab = 'vencidas';

    // Título de la página
    public function getTitle(): string
    {
        return 'Dashboard de Renovaciones';
    }

    // Contadores para los stats
    public function getVencidasCount(): int
    {
        return Linea::where('fecha_fin', '<', now())
            ->whereHas('cliente')
            ->count();
    }

    public function getT1Count(): int
    {
        return Linea::whereBetween('fecha_fin', [
            now(),
            now()->addDays(30)
        ])->whereHas('cliente')->count();
    }

    public function getT2Count(): int
    {
        return Linea::whereBetween('fecha_fin', [
            now()->addDays(31),
            now()->addDays(60)
        ])->whereHas('cliente')->count();
    }

    public function getT3Count(): int
    {
        return Linea::whereBetween('fecha_fin', [
            now()->addDays(61),
            now()->addDays(90)
        ])->whereHas('cliente')->count();
    }

    // Reemplaza el método getAllCount() existente con este
    public function getAllCount(): int
    {
        return $this->getVencidasCount() + $this->getT1Count() + $this->getT2Count() + $this->getT3Count();
    }

    // Configuración de la tabla
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getQueryForActiveTab())
            ->columns([
                TextColumn::make('nombre')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(Cliente $record): string => 'Cuenta: ' . $record->cuenta),

                // Columna para contar el total de líneas del cliente
                TextColumn::make('lineas_count')
                    ->counts('lineas') // Esto cuenta las líneas relacionadas
                    ->label('Total de Líneas')
                    ->sortable(),

                TextColumn::make('vendedor_asignado') // Usamos un nombre de columna único
                    ->label('Vendedor Asignado')
                    ->badge() // Para que se vea como una insignia
                    ->color(function (Cliente $record) { // El color cambia según el estado
                        $assignedUserIds = $record->lineas()->whereNotNull('usuario_id')->distinct()->pluck('usuario_id');

                        if ($assignedUserIds->isEmpty()) {
                            return 'gray'; // Sin asignar
                        }
                        if ($assignedUserIds->count() > 1) {
                            return 'warning'; // Varios vendedores
                        }
                        return 'success'; // Un solo vendedor
                    })
                    ->getStateUsing(function (Cliente $record) { // Lógica para determinar el texto
                        $assignedUserIds = $record->lineas()
                            ->whereNotNull('usuario_id')
                            ->distinct()
                            ->pluck('usuario_id');

                        if ($assignedUserIds->isEmpty()) {
                            return 'Sin asignar';
                        }

                        if ($assignedUserIds->count() > 1) {
                            return 'Asignación Mixta';
                        }

                        // Si solo hay un vendedor, muestra su nombre
                        $user = User::find($assignedUserIds->first());
                        return $user?->name; // Usamos '?' por si el usuario es eliminado
                    }),
            ])
            ->filters([
                TernaryFilter::make('asignacion')
                    ->label('Asignación')
                    ->placeholder('Todas')
                    ->trueLabel('Asignadas')
                    ->falseLabel('Sin Asignar')
                    ->queries(
                        true: fn(Builder $query) => $query->whereHas('lineas', fn($q) => $q->whereNotNull('usuario_id')),
                        false: fn(Builder $query) => $query->whereHas('lineas', fn($q) => $q->whereNull('usuario_id')),
                    ),
            ])
            ->actions([
                Action::make('ver_cliente')
                    ->label('Ver Información')
                    ->icon('heroicon-o-eye')
                    ->color('gray')
                    ->modalHeading(fn(Cliente $record) => "Líneas de: {$record->nombre}")
                    ->modalDescription(fn(Cliente $record) => "Cuenta: {$record->cuenta}")
                    ->modalWidth('4xl')

                    // Ahora apunta a la nueva vista intermediaria
                    ->modalContent(
                        fn(Cliente $record) =>
                        view('modals.renovaciones-lineas', ['record' => $record])
                    )

                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),

                // --- ACCIÓN PARA SUPERVISORES Y GERENTES (Protegida con el nuevo permiso) ---
                Action::make('asignarCliente')
                    ->label('Asignar a Vendedor')
                    ->icon('heroicon-o-user-group')->color('warning')
                    ->visible(function (Cliente $record): bool {
                        // --- CORREGIDO AQUÍ ---
                        return auth()->user()->can('action_assign_client') && $record->lineas()->whereNull('usuario_id')->exists();
                    })
                    ->form([
                        // --- CORREGIDO AQUÍ ---
                        Select::make('usuario_id')->label('Vendedor')->options(User::query()->pluck('name', 'id'))->required()->searchable(),
                    ])
                    ->action(function (Cliente $record, array $data): void {
                        // --- CORREGIDO AQUÍ ---
                        $lineasParaAsignar = $record->lineas()->whereNull('usuario_id')->get();
                        foreach ($lineasParaAsignar as $linea) {
                            $linea->update(['usuario_id' => $data['usuario_id']]);
                        }
                        Notification::make()->title('Asignación completada')->success()->send();
                    }),


                // --- ACCIÓN PARA VENDEDORES (Protegida con el nuevo permiso) ---
                Action::make('autoAsignar')
                    ->label('Tomar Cliente')
                    ->icon('heroicon-o-user-plus')->color('info')
                    ->visible(function (Cliente $record): bool {
                        // --- CORREGIDO AQUÍ ---
                        return auth()->user()->can('action_self_assign_client') && $record->lineas()->whereNull('usuario_id')->exists();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Tomar cliente')->modalDescription('¿Estás seguro?')->modalSubmitActionLabel('Sí, asignármelo')
                    ->action(function (Cliente $record): void {
                        // --- CORREGIDO AQUÍ ---
                        $lineasParaAsignar = $record->lineas()->whereNull('usuario_id')->get();
                        foreach ($lineasParaAsignar as $linea) {
                            $linea->update(['usuario_id' => auth()->id()]);
                        }
                        Notification::make()->title('Cliente asignado a ti')->success()->send();
                    }),

                Action::make('liberarAsignacion')
                    ->label('Liberar Cliente')
                    ->icon('heroicon-o-user-minus')->color('danger')
                    ->visible(fn(Cliente $record): bool => auth()->user()->can('action_unassign_client') && $record->lineas()->whereNotNull('usuario_id')->exists())
                    ->requiresConfirmation()->modalHeading('Liberar Cliente')->modalDescription('Se quitará la asignación de todas las líneas de este cliente. ¿Estás seguro?')
                    ->action(function (Cliente $record): void {
                        $record->lineas()->whereNotNull('usuario_id')->update([
                            'usuario_id' => null,
                            'asignado_en' => null,
                        ]);
                        Notification::make()->title('Cliente Liberado')->body('El cliente ahora está disponible para otros vendedores.')->success()->send();
                    }),

                Action::make('extenderPlazo')
                    ->label('Extender Plazo')
                    ->icon('heroicon-o-clock')->color('success')
                    ->visible(fn(Cliente $record): bool => auth()->user()->can('action_extend_assignment') && $record->lineas()->whereNotNull('usuario_id')->exists())
                    ->requiresConfirmation()->modalHeading('Extender Plazo')->modalDescription('Esto reiniciará el contador de 7 días para el vendedor asignado. ¿Continuar?')
                    ->action(function (Cliente $record): void {
                        $record->lineas()->whereNotNull('usuario_id')->update([
                            'asignado_en' => now(), // <-- REINICIA EL CONTADOR
                        ]);
                        Notification::make()->title('Plazo Extendido')->body('El vendedor tiene 7 días más para gestionar este cliente.')->success()->send();
                    }),
            ])
            ->bulkActions([]);
    }

    // Query según el tab activo
    protected function getQueryForActiveTab(): Builder
    {
        // La consulta base ahora es solo sobre el modelo Cliente
        $query = Cliente::query();

        return match ($this->activeTab) {

            'vencidas' => $query->whereHas('lineas', fn(Builder $q) => $q->where('fecha_fin', '<', now())),

            't1' => $query->whereHas('lineas', fn(Builder $q) => $q->whereBetween('fecha_fin', [now(), now()->addDays(30)])),

            't2' => $query->whereHas('lineas', fn(Builder $q) => $q->whereBetween('fecha_fin', [now()->addDays(31), now()->addDays(60)])),

            't3' => $query->whereHas('lineas', fn(Builder $q) => $q->whereBetween('fecha_fin', [now()->addDays(61), now()->addDays(90)])),

            // El 'all' que tenías antes no estaba en el match, lo agrego aquí como ejemplo si lo necesitas
            'all' => $query->whereHas('lineas', fn ($q) => $q->where('fecha_fin', '<', now()->addDays(90))),

            default => $query->whereHas('lineas', fn(Builder $q) => $q->where('fecha_fin', '<', now())),
        };
    }

    // Colores para los badges
    protected function getBadgeColor($record): string
    {
        $dias = $record->fecha_fin?->diffInDays(now(), false);

        if ($dias < 0) return 'danger';      // Vencida
        if ($dias <= 10) return 'danger';    // Crítico
        if ($dias <= 30) return 'warning';   // T1
        if ($dias <= 60) return 'info';      // T2
        return 'success';                    // T3
    }

    // Iconos para los badges
    protected function getBadgeIcon($record): string
    {
        $dias = $record->fecha_fin?->diffInDays(now(), false);

        if ($dias < 0) return 'heroicon-o-exclamation-triangle';
        if ($dias <= 10) return 'heroicon-o-fire';
        if ($dias <= 30) return 'heroicon-o-clock';
        return 'heroicon-o-check-circle';
    }


    // Cambiar tab activo
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    // Badge en navigation
    public static function getNavigationBadge(): ?string
    {
        $vencidas = Linea::where('fecha_fin', '<', now())
            ->whereHas('cliente')
            ->count();

        return $vencidas > 0 ? (string) $vencidas : null;
    }

    public function lineasClientesCount(Cliente $record): int
    {
        return $record->lineas()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }
}
