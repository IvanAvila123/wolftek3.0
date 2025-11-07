<?php

namespace App\Filament\Admin\Resources\Equipos\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EquiposTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_principal')
                ->label('Foto')
                ->circular(),
                TextColumn::make('nombre')
                    ->searchable(),
                TextColumn::make('capacidad')
                    ->searchable(),
                TextColumn::make('pantalla')
                    ->searchable(),
                TextColumn::make('resolucion')
                    ->searchable(),
                TextColumn::make('camara')
                    ->searchable(),
                TextColumn::make('sistema_operativo')
                    ->searchable(),
                TextColumn::make('foto_principal')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
