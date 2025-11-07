<?php

namespace App\Filament\Admin\Widgets;

use App\Filament\Admin\Resources\OportunidadResource; // Importa tu resource
use App\Filament\Admin\Resources\Oportunidads\OportunidadResource as OportunidadsOportunidadResource;
use App\Models\Oportunidad;
use Filament\Actions\Action;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class UltimasVentasTable extends BaseWidget
{
    protected static ?string $heading = 'Últimas Ventas Entregadas (Mes Actual)';
    protected static ?int $sort = 3; // Para que aparezca después del gráfico
    protected int | string | array $columnSpan = 'full'; // Ocupa todo el ancho

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Oportunidad::query()
                    ->where('estado', 'orden_entregada')
                    ->whereBetween('updated_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
                    ->latest('updated_at') // Muestra las más recientes primero
            )
            ->columns([
                Tables\Columns\TextColumn::make('cliente.nombre')
                    ->label('Cliente'),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Vendedor'),
                Tables\Columns\TextColumn::make('tipo_venta')
                    ->label('Tipo')
                    ->formatStateUsing(fn (string $state): string => config("oportunidad.tipos_venta.{$state}", $state))
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('updated_at') // O 'fecha_aprobacion'
                    ->label('Fecha Entrega')
                    ->dateTime('d/m/Y H:i'),
            ])
            // Acción para ir al detalle de la oportunidad
            ->actions([
                Action::make('Ver')
                     ->url(fn (Oportunidad $record): string => OportunidadsOportunidadResource::getUrl('view', ['record' => $record])),
            ]);
    }
}
