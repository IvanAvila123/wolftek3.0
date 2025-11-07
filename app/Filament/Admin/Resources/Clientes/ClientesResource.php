<?php

namespace App\Filament\Admin\Resources\Clientes;

use App\Filament\Admin\Resources\Clientes\Pages\CreateClientes;
use App\Filament\Admin\Resources\Clientes\Pages\EditClientes;
use App\Filament\Admin\Resources\Clientes\Pages\ListClientes;
use App\Filament\Admin\Resources\Clientes\Pages\ViewClientes;
use App\Filament\Admin\Resources\Clientes\RelationManagers\LineaRelationManager;
use App\Filament\Admin\Resources\Clientes\Schemas\ClientesForm;
use App\Filament\Admin\Resources\Clientes\Schemas\ClientesInfolist;
use App\Filament\Admin\Resources\Clientes\Tables\ClientesTable;
use App\Models\Cliente;
use App\Models\Clientes;
use App\Models\Linea;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class ClientesResource extends Resource
{
    protected static ?string $model = Cliente::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::BuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Gestión de Clientes';

    protected static ?string $recordTitleAttribute = 'nombre';

    protected static ?int $navigationSort = 1;

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
       $record->loadMissing('lineas'); 

        $dnInfo = '';
        // Si el cliente tiene líneas, las mostramos
        if ($record->lineas->isNotEmpty()) {
            $dns = $record->lineas->pluck('dn')->take(5)->implode(', '); // Toma hasta 5 DNs
            $dnInfo = " ({$dns})"; // Los pone entre paréntesis
        }

        // ESTE ES EL FORMATO DE TU IMAGEN
        return $record->nombre . ' (Cuenta: ' . $record->cuenta . ')' . $dnInfo;

    }

    public static function form(Schema $schema): Schema
    {
        return ClientesForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ClientesInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            LineaRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListClientes::route('/'),
            'create' => CreateClientes::route('/create'),
            'view' => ViewClientes::route('/{record}'),
            'edit' => EditClientes::route('/{record}/edit'),
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
