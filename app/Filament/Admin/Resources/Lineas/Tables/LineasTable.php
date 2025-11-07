<?php

namespace App\Filament\Admin\Resources\Lineas\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LineasTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Define the columns you want to display in the table
            TextColumn::make('dn')
                ->searchable()
                ->weight('bold')
                ->copyable()
                ->copyMessage('Dn copiado al portapapeles')
                ->sortable(),
            TextColumn::make('plan')
                ->searchable()
                ->copyable()
                ->sortable(),
            TextColumn::make('equipo')
                ->searchable()
                ->copyable()
                ->sortable(),
            TextColumn::make('clientes.nombre')
                ->label('Cliente')
                ->searchable()
                ->copyable()
                ->sortable(),
            TextColumn::make('fecha_fin')
                ->date('d/m/Y')
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
                ->toggleable(isToggledHiddenByDefault: true),
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
