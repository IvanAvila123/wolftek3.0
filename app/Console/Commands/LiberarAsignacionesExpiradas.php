<?php

namespace App\Console\Commands;

use App\Models\Linea;
use Illuminate\Console\Command;

class LiberarAsignacionesExpiradas extends Command
{
    protected $signature = 'asignaciones:liberar-expiradas';
    protected $description = 'Libera las líneas asignadas cuya fecha de asignación ha superado los 7 días.';

    public function handle()
    {
        $fechaLimite = now()->subDays(7);

        $lineasExpiradas = Linea::whereNotNull('usuario_id')
                                ->where('asignado_en', '<=', $fechaLimite)
                                ->get();

        if ($lineasExpiradas->isEmpty()) {
            $this->info('No se encontraron asignaciones expiradas.');
            return Command::SUCCESS;
        }

        $this->info("Se encontraron {$lineasExpiradas->count()} líneas expiradas. Liberando...");

        foreach ($lineasExpiradas as $linea) {
            $linea->update([
                'usuario_id' => null,
                'asignado_en' => null,
            ]);
        }

        $this->info('¡Proceso completado!');
        return Command::SUCCESS;
    }
}