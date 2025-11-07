<?php

namespace App\Filament\Admin\Pages\Auth;

use Filament\Auth\Pages\Login as PagesLogin;
use Filament\Forms\Components\TextInput;


class Login extends PagesLogin
{

    protected function getEmailFormComponent(): TextInput
    {
        return TextInput::make('username')
            ->label('Username')
            ->required()
            ->autofocus()
            ->extraInputAttributes(['tabindex' => 1])
            ->autocomplete();
    }

    // Cambia las credenciales para usar username
    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'username' => $data['username'],
            'password' => $data['password'],
        ];
    }

     // Asegura que los errores aparezcan en el campo username
    protected function throwFailureValidationException(): never
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            'data.username' => __('filament-panels::pages/auth/login.messages.failed'),
        ]);
    }
}
