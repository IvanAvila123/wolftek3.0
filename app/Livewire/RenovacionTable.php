<?php

namespace App\Livewire;

use App\Models\Cliente;
use App\Models\Linea;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder; // Importante para el query
use Illuminate\Database\Eloquent\Relations\HasMany;
use Livewire\Component;

use function Livewire\Volt\placeholder;

class RenovacionTable extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithActions;
    use InteractsWithForms;
    use InteractsWithTable;

    public int $clienteId;
    public ?Cliente $clienteRecord = null; // Para guardar el cliente una vez cargado

    // --- NUEVO: Propiedad para controlar la pestaña activa ---
    public string $activeTab = 'all';

    // El método mount inicializa el componente
    public function mount(int $clienteId): void
    {
        $this->clienteId = $clienteId;
        $this->clienteRecord = Cliente::findOrFail($clienteId);

        // --- NUEVO: Decidir la pestaña activa por defecto ---
        // Si hay líneas vencidas, esa será la pestaña inicial. Si no, serán todas.
        if ($this->getVencidasCount() > 0) {
            $this->activeTab = 'vencidas';
        }
    }

    // --- NUEVO: Métodos para calcular los contadores de cada pestaña ---
    public function getAllCount(): int
    {
        return $this->clienteRecord->lineas()->count();
    }
    public function getVencidasCount(): int
    {
        return $this->clienteRecord->lineas()->where('fecha_fin', '<', now())->count();
    }
    public function getT1Count(): int
    {
        return $this->clienteRecord->lineas()->whereBetween('fecha_fin', [now(), now()->addDays(30)])->count();
    }
    public function getT2Count(): int
    {
        return $this->clienteRecord->lineas()->whereBetween('fecha_fin', [now()->addDays(31), now()->addDays(60)])->count();
    }
    public function getT3Count(): int
    {
        return $this->clienteRecord->lineas()->whereBetween('fecha_fin', [now()->addDays(61), now()->addDays(90)])->count();
    }

    // --- NUEVO: Método para cambiar de pestaña ---
    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTablePage(); // Resetea la paginación de la tabla al cambiar de tab
    }

    // --- MODIFICADO: Ahora usamos `query()` en lugar de `relationship()` para un filtrado dinámico ---
    protected function getTableQuery(): Builder
{
    // --- ESTA ES LA LÍNEA QUE CAMBIA ---
    // En lugar de obtener la relación, iniciamos un Query Builder directamente
    // filtrado por el cliente actual.
    $query = Linea::query()->where('cliente_id', $this->clienteRecord->id);

    // Ahora, $query siempre es un Builder, y el resto del código funciona perfectamente.
    return match ($this->activeTab) {
        'vencidas' => $query->where('fecha_fin', '<', now()),
        't1' => $query->whereBetween('fecha_fin', [now(), now()->addDays(30)]),
        't2' => $query->whereBetween('fecha_fin', [now()->addDays(31), now()->addDays(60)]),
        't3' => $query->whereBetween('fecha_fin', [now()->addDays(61), now()->addDays(90)]),
        default => $query, // 'all' ahora devuelve el Builder original
    };
}

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery()) // Usamos el query dinámico
            ->columns([
                TextColumn::make('dn')->label('DN')->searchable()->sortable(),
                TextColumn::make('plan')->label('Plan')->searchable(),
                TextColumn::make('equipo')->label('Equipo')->placeholder('Sin equipo'),
                TextColumn::make('fecha_fin')->label('Vence')->date('d/m/Y')->sortable(),
                TextColumn::make('usuario.name')
                    ->label('Asignado a')
                    ->badge()
                    ->color('success')
                    ->placeholder('Sin asignar'),
                
            ])
            ->defaultSort('fecha_fin', 'asc')
            ->paginated([5, 10, 25])
            ->emptyStateHeading('No hay líneas en esta categoría.');
    }

    public function render()
    {
        return view('livewire.renovacion-table');
    }
}
