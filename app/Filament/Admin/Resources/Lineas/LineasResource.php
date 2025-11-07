<?php

namespace App\Filament\Admin\Resources\Lineas;

use App\Filament\Admin\Resources\Clientes\ClientesResource;
use App\Filament\Admin\Resources\Lineas\Pages\CreateLineas;
use App\Filament\Admin\Resources\Lineas\Pages\EditLineas;
use App\Filament\Admin\Resources\Lineas\Pages\ListLineas;
use App\Filament\Admin\Resources\Lineas\Pages\ViewLineas;
use App\Filament\Admin\Resources\Lineas\Schemas\LineasForm;
use App\Filament\Admin\Resources\Lineas\Schemas\LineasInfolist;
use App\Filament\Admin\Resources\Lineas\Tables\LineasTable;
use App\Models\Linea;
use App\Models\Lineas;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Collection;
use UnitEnum;

class LineasResource extends Resource
{
    protected static ?string $model = Linea::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhone;

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Clientes';
    protected static ?string $navigationParentItem = 'Clientes'; // debe coincidir con el label del nav del padre
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'dn';

    protected static bool $globallySearchable = false;

    public static function getGlobalSearchResultUrl(Model $record): ?string
{
    // $record es la Línea
    // $record->cliente es el Cliente
    
    // Verificamos que la línea tenga un cliente asignado
    if ($record->cliente) {
        return ClientesResource::getUrl('view', ['record' => $record->cliente]);
    }

    // Si por alguna razón no tiene cliente, no devuelve URL
    return null;
}

public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
{
    return $record->dn . ' (Cliente: ' . ($record->cliente ? $record->cliente->nombre : 'N/A') . ')';
}

    public static function form(Schema $schema): Schema
    {
        return LineasForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return LineasInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LineasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLineas::route('/'),
            'create' => CreateLineas::route('/create'),
            'view' => ViewLineas::route('/{record}'),
            'edit' => EditLineas::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }


    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
