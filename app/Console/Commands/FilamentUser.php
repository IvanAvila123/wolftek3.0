<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class FilamentUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:filament-admin';
    protected $description = 'Crea un usuario administrador para Filament';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->ask('¿Cuál es el nombre del administrador?');
        $username = $this->ask('¿Cuál es el nombre de usuario del administrador?');
        $email = $this->ask('¿Cuál es el correo electrónico del administrador?');
        $password = $this->secret('¿Cuál es la contraseña del administrador?');

        if (User::where('username', $username)->exists()) {
            $this->error("El usuario '$username' ya existe.");
            return 1; // Error code
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'username' => $username,
            'password' => Hash::make($password),
            // agrega más campos si lo requiere
        ]);

        $this->info("Usuario administrador '$name' creado exitosamente.");
        return 0; // Success code
    }
}
