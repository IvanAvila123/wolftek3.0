<?php

namespace App\Filament\Admin\Resources\Users\Tables;

use Dom\Text;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('username')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('roles.name')
                    ->label('Rol')
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
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                Action::make('resetPassword')
                    ->label('Restablecer Contraseña')
                    ->color('danger')
                    ->icon('heroicon-o-arrow-path')
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('new_password')
                            ->label('Nueva Contraseña')
                            ->password()
                            ->required()
                            ->minLength(8)
                            ->maxLength(64)
                            ->helperText('La nueva contraseña será enviada al correo del usuario.'),
                        TextInput::make('new_password_confirmation')
                            ->label('Confirmar Nueva Contraseña')
                            ->password()
                            ->required()
                            ->same('new_password')
                            ->minLength(8)
                            ->maxLength(64),
                        TextInput::make('user_email')
                            ->label('Correo del Usuario')
                            ->default(fn($record) => $record->email)
                            ->disabled()
                            ->readOnly(),
                    ])
                    ->action(function (array $data, $record) {
                        // Aquí va tu lógica para restablecer la contraseña
                        // Por ejemplo:
                        $record->update([
                            'password' => Hash::make($data['new_password'])
                        ]);

                        // Aquí puedes enviar el email con la nueva contraseña
                        // Mail::to($record->email)->send(new PasswordResetMail($data['new_password']));

                        // La notificación va aquí
                        Notification::make()
                            ->success()
                            ->title('Contraseña restablecida')
                            ->body("La nueva contraseña ha sido enviada al correo {$record->email}.")
                            ->send();
                    })
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
