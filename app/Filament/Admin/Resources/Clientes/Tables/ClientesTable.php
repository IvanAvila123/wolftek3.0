<?php

namespace App\Filament\Admin\Resources\Clientes\Tables;

use Dom\Text;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClientesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nombre')
                    ->searchable(isGlobal: true)
                    ->weight('bold')
                    ->sortable(),
                TextColumn::make('cuenta')
                    ->searchable(isGlobal: true)
                    ->copyable()
                    ->sortable(),
                TextColumn::make('id_cliente')
                    ->searchable(isGlobal: true)
                    ->copyable()
                    ->sortable(),
                TextColumn::make('representante')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('telefono')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('correo')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('rfc')
                    ->searchable()
                    ->copyable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->label('Eliminado'),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
