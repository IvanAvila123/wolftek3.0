<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detalles del Usuario')
                    ->description('Información y configuración del usuario')
                    ->icon('heroicon-o-user')
                    ->iconColor('primary')
                    ->collapsible()
                    ->compact(false)        // Elimina el espaciado compacto
                    ->columnSpanFull()      // Ocupa todo el ancho del contenedor
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Nombre del usuario')
                            ->required()
                            ->helperText('Nombre completo para el usuario'),
                        TextInput::make('username')
                            ->label('Usuario')
                            ->required()
                            ->unique()
                            ->validationMessages(
                                ['unique' => 'El usuario ya está en uso por otro usuario.']
                            )
                            ->minLength(5)
                            ->maxLength(10)
                            ->placeholder('hj325f'),
                        TextInput::make('email')
                            ->label('Correo')
                            ->required()
                            ->email()
                            ->unique()
                            ->validationMessages(
                                ['unique' => 'El correo ya está en uso por otro usuario.']
                            )
                            ->placeholder('ejemplo@wolftek.com')
                            ->helperText('Correo para el usuario'),
                        Select::make('roles') // <-- 1. Cambiado de 'role_id' a 'roles'
                            ->relationship('roles', 'name') // <-- 2. Relación en plural
                            ->label('Roles')
                            ->multiple() // <-- 3. Permite seleccionar más de un rol
                            ->required()
                            ->preload()
                            ->searchable()
                            ->helperText('Selecciona el rol o roles para este usuario'),
                        TextInput::make('password')
                            ->label('Contraseña')
                            ->password()
                            ->required()
                            ->same('password_confirmation')
                            ->revealable()
                            ->dehydrated(fn($state) => !empty($state))
                            ->visible(fn(string $operation): bool => $operation === 'create'),
                        TextInput::make('password_confirmation')
                            ->label('Confirmar Contraseña')
                            ->password()
                            ->revealable()
                            ->required(fn(string $operation): bool => $operation === 'create')
                            ->same('password')
                            ->dehydrated(fn($state) => !empty($state))
                            ->visible(fn(string $operation): bool => $operation === 'create'),

                    ])
            ]);
    }
}
