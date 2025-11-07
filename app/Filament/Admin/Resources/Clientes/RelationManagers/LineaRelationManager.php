<?php

namespace App\Filament\Admin\Resources\Clientes\RelationManagers;

use App\Filament\Admin\Resources\Clientes\ClientesResource;
use App\Filament\Admin\Resources\Lineas\LineasResource;
use App\Filament\Admin\Resources\Lineas\Pages\CreateLineas;
use Filament\Forms;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Collection;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Actions\CreateAction as TablesCreateAction;
use Filament\Tables\Actions\EditAction as TablesEditAction;
use Filament\Tables\Actions\DeleteAction as TablesDeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class LineaRelationManager extends RelationManager
{
    protected static string $relationship = 'lineas';


    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return auth()->user()->can('view_cliente_lineas_relation');
    }

    // Definir el formulario personalizado para Filament v4
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Información de la Línea')
                ->description('Complete los detalles de la nueva línea telefónica')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('dn')
                                ->label('DN (Directory Number)')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: 5551234567')
                                ->columnSpan(1),

                            DatePicker::make('fecha_fin')
                                ->label('Fecha Fin')
                                ->date('d/m/Y')
                                ->required()
                                ->columnSpan(1),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('plan')
                                ->label('Plan')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: ARMALO ATT 499 CTRL')
                                ->columnSpan(1),

                            TextInput::make('equipo')
                                ->label('Equipo')
                                ->default('Sin equipo asignado')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: iPhone 17, Samsung Galaxy S25')
                                ->columnSpan(1),
                        ]),

                    // Campo oculto para cliente_id (se llenará automáticamente)
                    Hidden::make('cliente_id')
                        ->default(fn () => $this->getOwnerRecord()->id),
                ])
                ->collapsible(),
        ]);
    }

    // Formulario específico para edición
    public function getEditFormSchema(): array
    {
        return [
            Section::make('Editar Línea Telefónica')
                ->description('Modifique los detalles de la línea telefónica')
                ->schema([
                    Grid::make(2)
                        ->schema([
                            TextInput::make('dn')
                                ->label('DN (Directory Number)')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: 5551234567')
                                ->columnSpan(1),

                            DatePicker::make('fecha_fin')
                                ->label('Fecha Fin')
                                ->date('d/m/Y')
                                ->required()
                                ->columnSpan(1),
                        ]),

                    Grid::make(2)
                        ->schema([
                            TextInput::make('plan')
                                ->label('Plan')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: ARMALO ATT 499 CTRL')
                                ->columnSpan(1),

                            TextInput::make('equipo')
                                ->label('Equipo')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Ej: iPhone 17, Samsung Galaxy S25')
                                ->columnSpan(1),
                        ]),

                    // Campo oculto para cliente_id (mantener el cliente actual)
                    Hidden::make('cliente_id')
                        ->default(fn () => $this->getOwnerRecord()->id),
                ])
                ->collapsible(),

            // Sección adicional con información del cliente (solo lectura)
            Section::make('Información del Cliente')
                ->description('Cliente asociado a esta línea')
                ->schema([
                    Placeholder::make('cliente_info')
                        ->label('Cliente')
                        ->content(function ($record) {
                            $cliente = $this->getOwnerRecord();
                            return $cliente->nombre . ' - ' . $cliente->cuenta;
                        }),

                    Placeholder::make('created_at')
                        ->label('Línea creada el')
                        ->content(fn ($record): string => $record?->created_at?->format('d/M/Y H:i') ?? '-'),

                    Placeholder::make('updated_at')
                        ->label('Última modificación')
                        ->content(fn ($record): string => $record?->updated_at?->diffForHumans() ?? '-'),
                ])
                ->collapsible()
                ->collapsed(),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('dn')
                    ->label('DN')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('DN copiado')
                    ->copyMessageDuration(1500),

                TextColumn::make('fecha_fin')
                    ->label('Fecha Fin')
                    ->date('d/m/Y')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('plan')
                    ->label('Plan')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('equipo')
                    ->label('Equipo')
                    ->searchable()
                    ->sortable()
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Puedes agregar filtros si los necesitas
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Nueva Línea')
                    ->icon('heroicon-o-plus')
                    ->schema($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        // Asegurar que el cliente_id esté presente
                        $data['cliente_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->actions([
                EditAction::make()
                    ->label('Editar')
                    ->icon('heroicon-o-pencil')
                    ->schema($this->getEditFormSchema()) // Usar formulario específico para edición
                    ->mutateFormDataUsing(function (array $data): array {
                        // Asegurar que el cliente_id no cambie
                        $data['cliente_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),

                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Eliminar Línea')
                    ->modalDescription('¿Está seguro que desea eliminar esta línea? Esta acción no se puede deshacer.')
                    ->modalSubmitActionLabel('Sí, eliminar'),
            ])
            ->bulkActions([
                // Acciones en lote si las necesitas
            ])
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Crear Primera Línea')
                    ->icon('heroicon-o-plus')
                    ->schema($this->getFormSchema())
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['cliente_id'] = $this->getOwnerRecord()->id;
                        return $data;
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    // Método para obtener el esquema del formulario principal
    protected function getFormSchema(): array
    {
        return $this->form(Schema::make())->getComponents();
    }

    // Personalizar el label del botón de creación
    public function getCreateActionLabel(): string
    {
        return 'Agregar Nueva Línea';
    }

    // Método para mutar datos antes de crear (adicional por seguridad)
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que siempre tenga el cliente_id correcto
        $data['cliente_id'] = $this->getOwnerRecord()->id;
        return $data;
    }

    // Método para mutar datos antes de guardar en edición
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // En caso de edición, mantener el cliente_id original
        $data['cliente_id'] = $this->getOwnerRecord()->id;
        return $data;
    }

    // Método adicional para personalizar el llenado del formulario al editar
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Puedes agregar lógica personalizada aquí si necesitas
        // modificar los datos antes de llenar el formulario
        return $data;
    }
}
