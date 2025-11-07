<?php

namespace App\Filament\Admin\Pages;

use App\Models\Equipo;
use BackedEnum;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use UnitEnum;

class CatalogoEquipos extends Page
{
    use HasPageShield;
    protected string $view = 'filament.admin.pages.catalogo-equipos';

    protected static string|UnitEnum|null $navigationGroup = 'Gestion De Ventas';

    protected static ?string $title = 'Catálogo de Equipos';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DeviceTablet;

    public Collection $equipos;

    public function mount(): void
    {
        // Carga todos los equipos y los guarda en la propiedad $equipos
        $this->equipos = Equipo::orderBy('nombre')->get();
    }
}
