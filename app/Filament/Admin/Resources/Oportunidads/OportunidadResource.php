<?php

namespace App\Filament\Admin\Resources\Oportunidads;

use App\Filament\Admin\Resources\Oportunidads\Pages\CreateOportunidad;
use App\Filament\Admin\Resources\Oportunidads\Pages\EditOportunidad;
use App\Filament\Admin\Resources\Oportunidads\Pages\ListOportunidads;
use App\Filament\Admin\Resources\Oportunidads\Pages\ViewOportunidad;
use App\Filament\Admin\Resources\Oportunidads\Schemas\OportunidadForm;
use App\Filament\Admin\Resources\Oportunidads\Schemas\OportunidadInfolist;
use App\Filament\Admin\Resources\Oportunidads\Tables\OportunidadsTable;
use App\Models\Oportunidad;
use BackedEnum;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class OportunidadResource extends Resource
{
    protected static ?string $model = Oportunidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::DocumentCheck;

    protected static ?string $recordTitleAttribute = 'cliente.nombre';

    protected static ?string $modelLabel = 'Oportunidad';

    protected static string|UnitEnum|null $navigationGroup = 'Gestion De Ventas';

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->cliente->nombre . ' - ' . $record->id;
    }

    public static function form(Schema $schema): Schema
    {
        return OportunidadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return OportunidadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OportunidadsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\LineasOportunidadRelationManager::class,
            RelationManagers\DocumentosOportunidadRelationManager::class,
            RelationManagers\RevisionOportunidadRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOportunidads::route('/'),
            'create' => CreateOportunidad::route('/create'),
            'view' => ViewOportunidad::route('/{record}'),
            'edit' => EditOportunidad::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::count() > 10 ? 'warning' : 'primary';
    }

    public static function getNavigationBadge(): ?string
{
    $user = auth()->user();

    // 1. Verifica si el usuario tiene el permiso para ver todo
    if ($user->can('view_all_oportunidades')) {

        // 2. Si tiene permiso, cuenta TODAS las oportunidades pendientes
        $count = static::getModel()::where('estado', 'Pendiente')->count();

        // 3. Devuelve el contador si es mayor que cero, si no, null
        return $count > 0 ? (string)$count : null;

    } else {
        // 4. Si NO tiene el permiso, no muestra ningún contador
        return null;
    }
}

    public static function getEloquentQuery(): Builder
    {
        $user = auth()->user();

        $query = parent::getEloquentQuery();

        if (!$user->can('view_all_oportunidades')) {
        $query->where('usuario_id', $user->id);
    }

        return $query;
    }
}
