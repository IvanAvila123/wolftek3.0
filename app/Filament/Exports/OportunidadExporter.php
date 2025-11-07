<?php

namespace App\Filament\Exports;

use App\Models\Oportunidad;
use App\Models\OportunidadLinea;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OportunidadExporter extends Exporter
{
    protected static ?string $model = Oportunidad::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID Oportunidad'),

            // Usamos la "notación de puntos" de la documentación
            // para relaciones (Sección: "Displaying data from relationships")
            ExportColumn::make('cliente.nombre')
                ->label('Cliente'),

            ExportColumn::make('usuario.name') // (Asumo 'name' para el usuario)
                ->label('Vendedor'),

            ExportColumn::make('estado')
                ->label('Estado'),

            // --- ¡Aquí está la magia! ---
            // Usamos la sección: "Calculated export column state"
            ExportColumn::make('dn') // Nombre interno
                ->label('DNs')
                ->state(function (Oportunidad $record): string {
                    // $record es la Oportunidad de esa fila

                    // Cargamos las relaciones para ser eficientes
                    $record->load('oportunidadLinea.linea');

                    // Mapeamos los resultados
                    $dns = $record->oportunidadLinea->map(function ($opLinea) {
                        return $opLinea->linea ? $opLinea->linea->dn : null;
                    })->filter();

                    // Devolvemos el string final
                    return $dns->implode(', ');
                }),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your oportunidad export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
