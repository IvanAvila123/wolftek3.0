<?php

namespace App\Filament\Admin\Resources\RevisionOportunidads;

use App\Filament\Admin\Resources\RevisionOportunidads\Pages\CreateRevisionOportunidad;
use App\Filament\Admin\Resources\RevisionOportunidads\Pages\EditRevisionOportunidad;
use App\Filament\Admin\Resources\RevisionOportunidads\Pages\ListRevisionOportunidads;
use App\Filament\Admin\Resources\RevisionOportunidads\Pages\ViewRevisionOportunidad;
use App\Filament\Admin\Resources\RevisionOportunidads\Schemas\RevisionOportunidadForm;
use App\Filament\Admin\Resources\RevisionOportunidads\Schemas\RevisionOportunidadInfolist;
use App\Filament\Admin\Resources\RevisionOportunidads\Tables\RevisionOportunidadsTable;
use App\Models\RevisionOportunidad;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use UnitEnum;

class RevisionOportunidadResource extends Resource
{
    protected static ?string $model = RevisionOportunidad::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $modelLabel = 'Revisiones';

    protected static string|UnitEnum|null $navigationGroup = 'Gestion De Ventas';

    public static function form(Schema $schema): Schema
    {
        return RevisionOportunidadForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return RevisionOportunidadInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return RevisionOportunidadsTable::configure($table);
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
            'index' => ListRevisionOportunidads::route('/'),
            'create' => CreateRevisionOportunidad::route('/create'),
            'view' => ViewRevisionOportunidad::route('/{record}'),
            'edit' => EditRevisionOportunidad::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
