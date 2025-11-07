<?php

namespace App\Filament\Admin\Resources\Oportunidads\RelationManagers;

use App\Models\DocumentoOportunidad;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use GuzzleHttp\Psr7\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class DocumentosOportunidadRelationManager extends RelationManager
{
    protected static string $relationship = 'documentoOportunidad';

    protected static ?string $recordTitleAttribute = 'nombre_archivo';

    protected static ?string $title = 'Documentos';

    protected static ?string $modelLabel = 'Documento';

    protected static ?string $pluralModelLabel = 'Documentos';


    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre_archivo')
            ->columns([
                IconColumn::make('tipo_documento')
                    ->label('')
                    ->icon(fn(DocumentoOportunidad $record): string => $this->getDocumentIcon($record->tipo_documento))
                    ->color(fn(DocumentoOportunidad $record): string => $this->getDocumentColors($record->tipo_documento))
                    ->size('lg')
                    ->tooltip(fn(DocumentoOportunidad $record): string => $this->formatDocumentLabel($record->tipo_documento)),

                TextColumn::make('tipo_documento')
                    ->label('Tipo')
                    ->formatStateUsing(fn(string $state): string => $this->formatDocumentLabel($state))
                    ->badge()
                    ->color(fn(DocumentoOportunidad $record): string => $this->getDocumentColors($record->tipo_documento))
                    ->searchable()
                    ->sortable(),

                BadgeColumn::make('status')
                    ->label('Estado')
                    ->getStateUsing(fn(DocumentoOportunidad $record): string => $this->getDocumentStatus($record))
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->colors([
                        'success' => 'Obligatorio Completo',
                        'warning' => 'Condicional',
                        'gray' => 'Opcional',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'Obligatorio Completo',
                        'heroicon-o-exclamation-triangle' => 'Condicional',
                        'heroicon-o-information-circle' => 'Opcional',
                    ]),

                TextColumn::make('nombre_archivo')
                    ->label('Archivo')
                    ->limit(30)
                    ->tooltip(function (DocumentoOportunidad $record): string {
                        return $record->nombre_archivo . "\nTamaño: " . $this->getFileSize($record->ruta);
                    })
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Subido')
                    ->date('d/m/y H:i')
                    ->tooltip(fn(DocumentoOportunidad $record): string => $record->created_at->format('d/m/Y H:i:s'))
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->label('Modificado')
                    ->date('d/m/y H:i')
                    ->tooltip(fn(DocumentoOportunidad $record): string => $record->updated_at->format('d/m/Y H:i:s'))
                    ->sortable(),

                TextColumn::make('usuario.name')
                    ->label('Usuario')
                    ->limit(20)
                    ->tooltip(fn(DocumentoOportunidad $record): string => $record->usuario->name ?? 'Sin usuario')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('tipo_documento')
                    ->label('Tipo de Documento')
                    ->options($this->getAllDocumentTypes())
                    ->searchable()
                    ->multiple(),

                SelectFilter::make('status')
                    ->label('Estado')
                    ->options([
                        'obligatorio' => 'Obligatorio',
                        'condicional' => 'Condicional',
                        'opcional' => 'Opcional',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        $oportunidad = $this->getOwnerRecord();
                        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';
                        $documentos = include(config_path('documentos.php'));

                        if (!isset($documentos[$tipoVenta])) {
                            return $query;
                        }

                        $tipos = collect($documentos[$tipoVenta])
                            ->filter(function ($doc) use ($data) {
                                return match ($data['value']) {
                                    'obligatorio' => $doc['obligatorio'] === true,
                                    'condicional' => $doc['obligatorio'] === 'condicional',
                                    'opcional' => !in_array($doc['obligatorio'], [true, 'condicional']),
                                    default => true
                                };
                            })
                            ->pluck('tipo')
                            ->toArray();

                        return $query->whereIn('tipo_documento', $tipos);
                    }),
            ])
            ->headerActions([
                Action::make('subirDocumento')
                    ->label('Subir Documento')
                    ->visible(function (): bool {
                        $oportunidad = $this->getOwnerRecord();
                        return !in_array(strtolower($oportunidad->estado), ['aprobada', 'cancelada', 'orden_entregada']);
                    })
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->modalHeading('Subir Nuevo Documento')
                    ->modalDescription(fn() => 'Tipo de venta: ' . $this->getTipoVentaLabel())
                    ->modalWidth('2xl')
                    ->form([
                        Section::make('Información del Documento')
                            ->schema([
                                Select::make('tipo_documento')
                                    ->label('Tipo de Documento')
                                    ->options($this->getDocumentOptionsForCurrentType())
                                    ->required()
                                    ->searchable()
                                    ->live()
                                    ->helperText(fn($state) => $this->getDocumentHelperText($state)),

                                Placeholder::make('document_status')
                                    ->label('Estado')
                                    ->content(function ($get) {
                                        $tipo = $get('tipo_documento');
                                        if (!$tipo) return '';

                                        $oportunidad = $this->getOwnerRecord();
                                        $status = $this->getDocumentStatusByType($oportunidad, $tipo);
                                        $color = match ($status) {
                                            'Obligatorio Completo' => 'danger',
                                            'Condicional' => 'warning',
                                            default => 'success'
                                        };

                                        return new HtmlString(
                                            "<span class='inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-{$color}-100 text-{$color}-800'>" .
                                                $status .
                                                "</span>"
                                        );
                                    }),

                                FileUpload::make('archivo')
                                    ->label('Archivo')
                                    ->disk('public')
                                    ->directory('documentos_oportunidad')
                                    ->preserveFilenames()
                                    ->acceptedFileTypes($this->getAllowedFileTypes())
                                    ->maxSize(10240) // 10MB
                                    ->required()
                                    ->helperText('Formatos: PDF, Imágenes, Office. Máximo: 10MB')
                                    ->live()
                                    ->afterStateUpdated(function ($state, Set $set) {
                                        if ($state instanceof TemporaryUploadedFile) {
                                            $set('nombre_original', $state->getClientOriginalName());
                                        }
                                    }),

                                TextInput::make('nombre_original')
                                    ->label('Nombre del Archivo')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),

                        Section::make('Estado de Completitud')
                            ->description('Revisa el progreso de documentos requeridos')
                            ->icon('heroicon-o-check-circle')
                            ->schema([
                                Placeholder::make('completion_status')
                                    ->label('')
                                    ->content(function () {
                                        $oportunidad = $this->getOwnerRecord();
                                        $status = $this->getDocumentCompletionStatus($oportunidad);

                                        $colorClass = match ($status['type']) {
                                            'success' => 'text-green-700 bg-green-50 border-green-200',
                                            'warning' => 'text-yellow-700 bg-yellow-50 border-yellow-200',
                                            default => 'text-blue-700 bg-blue-50 border-blue-200'
                                        };

                                        return new HtmlString(
                                            "<div class='p-4 rounded-lg border {$colorClass}'>" .
                                                "<h4 class='font-medium mb-2'>{$status['title']}</h4>" .
                                                "<p class='text-sm whitespace-pre-line'>{$status['message']}</p>" .
                                                "</div>"
                                        );
                                    }),
                            ])
                            ->collapsible()
                            ->persistCollapsed(),
                    ])
                    ->action(function (array $data) {
                        try {
                            // Validar que se tengan los datos necesarios
                            if (!isset($data['tipo_documento']) || !isset($data['archivo'])) {
                                throw new \Exception('Faltan datos requeridos');
                            }

                            // Crear el documento
                            $documento = $this->getOwnerRecord()->documentoOportunidad()->create([
                                'tipo_documento' => $data['tipo_documento'],
                                'ruta' => $data['archivo'],
                                'nombre_archivo' => $data['nombre_original'] ?? basename($data['archivo']),
                                'usuario_id' => auth()->id(),
                            ]);

                            // Verificar completitud
                            $this->checkDocumentCompleteness($this->getOwnerRecord());

                            Notification::make()
                                ->title('Documento subido exitosamente')
                                ->body("Documento '{$this->formatDocumentLabel($data['tipo_documento'])}' agregado correctamente")
                                ->success()
                                ->send();

                            // Refrescar la tabla
                            $this->dispatch('refresh');
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al subir documento')
                                ->body('Ocurrió un error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('verificarDocumentos')
                    ->label('Verificar Completitud')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->modalHeading(function () {
                        $resultado = $this->getDocumentCompletionStatus($this->getOwnerRecord());
                        return $resultado['title'];
                    })
                    ->modalDescription(function () {
                        $resultado = $this->getDocumentCompletionStatus($this->getOwnerRecord());
                        return $resultado['message'];
                    })
                    ->modalContent(function () {
                        $oportunidad = $this->getOwnerRecord();
                        $faltantes = $this->getMissingDocuments($oportunidad);
                        $presentes = $this->getPresentDocuments($oportunidad);
                        $condicionales = $this->getConditionalDocuments($oportunidad);

                        $html = '<div class="space-y-4">';

                        // Documentos presentes
                        if (!empty($presentes)) {
                            $html .= '<div class="bg-green-50 p-4 rounded-lg border border-green-200">';
                            $html .= '<h4 class="font-medium text-green-800 mb-2">✅ Documentos Completos (' . count($presentes) . ')</h4>';
                            $html .= '<ul class="text-sm text-green-700 space-y-1">';
                            foreach ($presentes as $doc) {
                                $html .= '<li>• ' . htmlspecialchars($this->formatDocumentLabel($doc)) . '</li>';
                            }
                            $html .= '</ul></div>';
                            $html .= '<br>';
                        }

                        // Documentos faltantes
                        if (!empty($faltantes)) {
                            $html .= '<div class="bg-red-50 p-4 rounded-lg border border-red-200">';
                            $html .= '<h4 class="font-medium text-red-800 mb-2">❌ Documentos Faltantes (' . count($faltantes) . ')</h4>';
                            $html .= '<ul class="text-sm text-red-700 space-y-1">';
                            foreach ($faltantes as $doc) {
                                $html .= '<li>• ' . htmlspecialchars($this->formatDocumentLabel($doc)) . '</li>';
                            }
                            $html .= '</ul></div>';
                            $html .= '<br>';
                        }

                        // Documentos condicionales
                        if (!empty($condicionales)) {
                            $html .= '<div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">';
                            $html .= '<h4 class="font-medium text-yellow-800 mb-2">⚠️ Documentos Condicionales (' . count($condicionales) . ')</h4>';
                            $html .= '<ul class="text-sm text-yellow-700 space-y-1">';
                            foreach ($condicionales as $doc) {
                                $html .= '<li>• ' . htmlspecialchars($this->formatDocumentLabel($doc)) . '</li>';
                            }
                            $html .= '</ul></div>';
                        }

                        $html .= '</div>';

                        return new HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),



                Action::make('document_info')
                    ->label('Info Requerida')
                    ->icon('heroicon-o-information-circle')
                    ->color('info')
                    ->modalHeading('Documentos Requeridos')
                    ->modalDescription(fn() => 'Tipo de venta: ' . $this->getTipoVentaLabel())
                    ->modalContent(fn() => $this->getRequiredDocumentsInfoView())
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Cerrar'),
            ])
            ->actions([
                Action::make('ver')
                    ->label('Ver')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->tooltip('Ver documento en nueva pestaña')
                    ->url(fn($record) => $this->getDocumentUrl($record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $this->documentExists($record)),

                Action::make('descargar')
                    ->label('Descargar')
                    ->visible(function (): bool {
                        $oportunidad = $this->getOwnerRecord();
                        return !in_array(strtolower($oportunidad->estado), ['aprobada', 'cancelada']);
                    })
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->tooltip('Descargar documento')
                    ->action(function ($record) {
                        if ($this->documentExists($record)) {
                            return Storage::disk('public')->download($record->ruta, $record->nombre_original);
                        } else {
                            Notification::make()
                                ->title('Archivo no encontrado')
                                ->body('El archivo no existe en el servidor')
                                ->warning()
                                ->send();
                        }
                    }),

                Action::make('editar')
                    ->label('Editar')
                    ->visible(function (): bool {
                        $oportunidad = $this->getOwnerRecord();
                        return !in_array(strtolower($oportunidad->estado), ['aprobada', 'cancelada', 'orden_entregada']);
                    })
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->tooltip('Editar documento')
                    ->modalHeading('Editar Documento')
                    ->modalWidth('2xl')
                    ->form([
                        Section::make('Información Actual')
                            ->description(fn ($record) => "Editando: {$record->nombre_original}")
                            ->schema([
                                Select::make('tipo')
                                    ->label('Tipo de Documento')
                                    ->options($this->getDocumentOptionsForCurrentType())
                                    ->required()
                                    ->searchable()
                                    ->live(),

                                FileUpload::make('archivo')
                                    ->label('Reemplazar Archivo (Opcional)')
                                    ->disk('public')
                                    ->directory('documentos_oportunidad')
                                    ->preserveFilenames()
                                    ->acceptedFileTypes($this->getAllowedFileTypes())
                                    ->maxSize(10240)
                                    ->helperText('Deje vacío para mantener el archivo actual')
                                    ->live()
                                    ->afterStateUpdated(function ($state, callable $set) {
                                        if ($state && $state instanceof UploadedFile) {
                                            $set('nombre_original', $state->getClientOriginalName());
                                        }
                                    }),

                                TextInput::make('nombre_original')
                                    ->label('Nombre del Archivo')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ])
                    ->fillForm(fn($record) => [
                        'tipo' => $record->tipo,
                        'nombre_original' => $record->nombre_original,
                    ])
                    ->action(function ($record, array $data) {
                        try {
                            $updateData = ['tipo' => $data['tipo']];

                            if (isset($data['archivo']) && $data['archivo']) {
                                // Eliminar archivo anterior
                                if ($record->ruta && Storage::disk('public')->exists($record->ruta)) {
                                    Storage::disk('public')->delete($record->ruta);
                                }

                                $updateData['ruta'] = $data['archivo'];
                                $updateData['nombre_original'] = $data['nombre_original'] ?? 'archivo_actualizado';
                            }

                            $record->update($updateData);

                            Notification::make()
                                ->title('Documento actualizado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al actualizar')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('eliminar')
                    ->label('Eliminar')
                    ->visible(function (): bool {
                        $oportunidad = $this->getOwnerRecord();
                        return !in_array(strtolower($oportunidad->estado), ['aprobada', 'cancelada', 'orden_entregada']);
                    })
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->tooltip('Eliminar documento')
                    ->requiresConfirmation()
                    ->modalHeading('Confirmar Eliminación')
                    ->modalDescription(fn ($record) => "¿Eliminar '{$record->nombre_original}'?")
                    ->modalSubmitActionLabel('Eliminar')
                    ->action(function ($record) {
                        try {
                            if (Storage::disk('public')->exists($record->ruta)) {
                                Storage::disk('public')->delete($record->ruta);
                            }
                            $record->delete();

                            Notification::make()
                                ->title('Documento eliminado')
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('Error al eliminar')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                DeleteBulkAction::make()
                    ->requiresConfirmation()
                    ->after(function () {
                        $this->showCompletionNotification();
                    }),
            ])
            ->emptyStateHeading('No hay documentos')
            ->emptyStateDescription('Comienza subiendo los documentos requeridos para esta oportunidad.')
            ->emptyStateIcon('heroicon-o-document-plus')
            ->emptyStateActions([
                CreateAction::make()
                    ->label('Subir primer documento')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Actualización automática cada 30 segundos
    }

    // Métodos auxiliares existentes mejorados...

    public function formatDocumentLabel(string $tipo): string
    {
        $labels = [
            'INE' => 'INE - Identificación Oficial',
            'OP' => 'OP - Orden de Programación',
            'OC' => 'OC - Orden de Contratación',
            'FORMATO SAE' => 'Formato SAE',
            'FOLIO DE AUTORIZACION' => 'Folio de Autorización',
            'ACTA CONSTITUTIVA' => 'Acta Constitutiva',
            'CARTA BURO' => 'Carta de Buró de Crédito',
            'COMPROBANTE DE DOMICILIO' => 'Comprobante de Domicilio',
            'RFC' => 'RFC (Cédula de Identificación Fiscal)',
            'ESTADO DE CUENTA' => 'Estado de Cuenta',
            'FORMATO DE ACCIONISTAS' => 'Formato de Accionistas',
            'DOCUMENTO ADICIONAL' => 'Documento Adicional',
            'motivo_rechazo' => 'Motivo de Rechazo',
        ];

        return $labels[$tipo] ?? ucfirst(str_replace('_', ' ', $tipo));
    }

    private function getDocumentOptionsForCurrentType(): array
    {
        $oportunidad = $this->getOwnerRecord();
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        $documentos = include(config_path('documentos.php'));
        $options = [];

        if (isset($documentos[$tipoVenta])) {
            foreach ($documentos[$tipoVenta] as $doc) {
                $tipo = $doc['tipo'];
                $label = $this->formatDocumentLabel($tipo);

                // Añadir indicador de obligatoriedad
                if ($doc['obligatorio'] === true) {
                    $label .= ' (Obligatorio)';
                } elseif ($doc['obligatorio'] === 'condicional') {
                    $label .= ' (Condicional)';
                }else {
                    $label .= ' (Opcional)';
                }

                $options[$tipo] = $label;
            }
        }

        return $options;
    }

    private function getDocumentColors(string $tipo): string
    {
        $colors = [
            'INE' => 'primary',
            'OP' => 'success',
            'OC' => 'warning',
            'FORMATO SAE' => 'info',
            'FOLIO DE AUTORIZACION' => 'secondary',
            'ACTA CONSTITUTIVA' => 'purple',
            'CARTA BURO' => 'indigo',
            'COMPROBANTE DE DOMICILIO' => 'teal',
            'RFC' => 'cyan',
            'ESTADO DE CUENTA' => 'orange',
            'FORMATO DE ACCIONISTAS' => 'pink',
            'DOCUMENTO ADICIONAL' => 'gray',
            'motivo_rechazo' => 'red',
        ];

        return $colors[$tipo] ?? 'gray';
    }

    private function getAllowedFileTypes(): array
    {
        return [
            'application/pdf',
            'image/*',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
    }

    private function getDocumentIcon(string $tipo): string
    {
        $icons = [
            'INE' => 'heroicon-o-identification',
            'OP' => 'heroicon-o-document-text',
            'OC' => 'heroicon-o-clipboard-document-check',
            'FORMATO SAE' => 'heroicon-o-document-duplicate',
            'FOLIO DE AUTORIZACION' => 'heroicon-o-check-badge',
            'ACTA CONSTITUTIVA' => 'heroicon-o-book-open',
            'CARTA BURO' => 'heroicon-o-credit-card',
            'COMPROBANTE DE DOMICILIO' => 'heroicon-o-home',
            'RFC' => 'heroicon-o-archive-box',
            'ESTADO DE CUENTA' => 'heroicon-o-currency-dollar',
            'FORMATO DE ACCIONISTAS' => 'heroicon-o-users',
            'DOCUMENTO ADICIONAL' => 'heroicon-o-document-plus',
            'motivo_rechazo' => 'heroicon-o-exclamation-triangle',
        ];

        return $icons[$tipo] ?? 'heroicon-o-document';
    }

    private function getFileSize(string $path): string
    {
        if (!$path || !Storage::disk('public')->exists($path)) {
            return 'Archivo no encontrado';
        }

        $size = Storage::disk('public')->size($path);
        return $this->formatBytes($size);
    }

    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }

    private function getDocumentStatus($record): string
    {
        $oportunidad = $this->getOwnerRecord();
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        return $this->getDocumentStatusByType($oportunidad, $record->tipo_documento);
    }

    private function getDocumentStatusByType($oportunidad, string $tipo): string
    {
        $documentos = include(config_path('documentos.php'));
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        if (!isset($documentos[$tipoVenta])) {
            return 'Opcional';
        }

        $documento = collect($documentos[$tipoVenta])
            ->firstWhere('tipo', $tipo);

        if (!$documento) {
            return 'Opcional';
        }

        return match ($documento['obligatorio']) {
            true => 'Obligatorio Completo',
            'condicional' => 'Condicional',
            default => 'Opcional'
        };
    }

    private function getDocumentHelperText(?string $tipo): string
    {
        if (!$tipo) return '';

        $help = [
            'INE' => 'Subir identificación oficial vigente (frente y reverso)',
            'COMPROBANTE DE DOMICILIO' => 'Recibo de servicios no mayor a 3 meses',
            'CARTA BURO' => 'Carta de buró de crédito actualizada',
            'RFC' => 'Cédula de identificación fiscal',
            'ACTA CONSTITUTIVA' => 'Documento legal de constitución de la empresa',
            'ESTADO DE CUENTA' => 'Estado de cuenta bancario reciente',
            'OP' => 'Orden de programación autorizada',
            'OC' => 'Orden de contratación firmada',
            'FORMATO SAE' => 'Formato del Sistema de Administración Empresarial',
            'FOLIO DE AUTORIZACION' => 'Folio de autorización oficial',
            'FORMATO DE ACCIONISTAS' => 'Información detallada de accionistas',
            'DOCUMENTO ADICIONAL' => 'Cualquier documento relevante adicional',
            'motivo_rechazo' => 'Documento explicando motivo de rechazo',
        ];

        return $help[$tipo] ?? '';
    }

    private function getDocumentUrl($record): ?string
    {
        return $record->ruta ? Storage::disk('public')->url($record->ruta) : null;
    }

    private function documentExists($record): bool
    {
        return $record->ruta && Storage::disk('public')->exists($record->ruta);
    }

    private function checkDocumentCompleteness($oportunidad): void
    {
        $resultado = $this->getDocumentCompletionStatus($oportunidad);

        if ($resultado['type'] === 'success') {
            // Opcional: actualizar estado de la oportunidad si está completa
            // $oportunidad->update(['documentos_completos' => true]);
        }
    }

    private function showCompletionNotification(): void
    {
        $oportunidad = $this->getOwnerRecord();
        $status = $this->getDocumentCompletionStatus($oportunidad);

        $notification = Notification::make()
            ->title($status['title'])
            ->body($status['message']);

        match ($status['type']) {
            'success' => $notification->success(),
            'warning' => $notification->warning(),
            default => $notification->info()
        };

        $notification->send();
    }

    private function getDocumentCompletionStatus($oportunidad): array
    {
        $faltantes = $this->getMissingDocuments($oportunidad);
        $condicionales = $this->getConditionalDocuments($oportunidad);
        $presentes = $this->getPresentDocuments($oportunidad);

        if (empty($faltantes)) {
            $message = "✅ Todos los documentos obligatorios están presentes (" . count($presentes) . " documentos).";
            if (!empty($condicionales)) {
                $message .= "\nCondicionales presentes: " . implode(', ', array_map(fn($doc) => $this->formatDocumentLabel($doc), $condicionales));
            }

            return [
                'title' => 'Documentos Completos',
                'message' => $message,
                'type' => 'success'
            ];
        } else {
            $message = "⚠️ Faltan " . count($faltantes) . " documentos obligatorios:\n" .
                implode(', ', array_map(fn($doc) => $this->formatDocumentLabel($doc), $faltantes));

            return [
                'title' => 'Documentos Incompletos',
                'message' => $message,
                'type' => 'warning'
            ];
        }
    }

    private function getPresentDocuments($oportunidad): array
    {
        $documentos = include(config_path('documentos.php'));
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        if (!isset($documentos[$tipoVenta])) {
            return [];
        }

        $obligatorios = collect($documentos[$tipoVenta])
            ->where('obligatorio', true)
            ->pluck('tipo')
            ->toArray();

        $existentes = $oportunidad->documentoOportunidad()
            ->pluck('tipo_documento')
            ->toArray();

        return array_intersect($obligatorios, $existentes);
    }

    private function getRequiredDocumentsInfoView(): HtmlString
    {
        $oportunidad = $this->getOwnerRecord();
        $info = $this->getRequiredDocumentsInfo($oportunidad);

        return new HtmlString(
            "<div class='prose max-w-none'>" .
                "<pre class='whitespace-pre-wrap text-sm bg-gray-50 p-4 rounded-lg dark:bg-cyan-950'>" .
                htmlspecialchars($info['message']) .
                "</pre>" .
                "</div>"
        );
    }

    private function getRequiredDocumentsInfo($oportunidad): array
    {
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';
        $documentos = include(config_path('documentos.php'));

        if (!isset($documentos[$tipoVenta])) {
            return [
                'message' => 'No hay configuración de documentos para este tipo de venta.',
                'type' => 'info'
            ];
        }

        $obligatorios = collect($documentos[$tipoVenta])
            ->where('obligatorio', true)
            ->pluck('tipo')
            ->map(fn($tipo) => $this->formatDocumentLabel($tipo))
            ->toArray();

        $condicionales = collect($documentos[$tipoVenta])
            ->where('obligatorio', 'condicional')
            ->pluck('tipo')
            ->map(fn($tipo) => $this->formatDocumentLabel($tipo))
            ->toArray();

        $message = "Tipo: " . $this->getTipoVentaLabel() . "\n\n";

        if (!empty($obligatorios)) {
            $message .= "📋 OBLIGATORIOS (" . count($obligatorios) . "):\n";
            $message .= "• " . implode("\n• ", $obligatorios) . "\n\n";
        }

        if (!empty($condicionales)) {
            $message .= "⚠️ CONDICIONALES (" . count($condicionales) . "):\n";
            $message .= "• " . implode("\n• ", $condicionales);
        }

        return [
            'message' => $message,
            'type' => 'info'
        ];
    }

    private function getTipoVentaLabel(): string
    {
        $oportunidad = $this->getOwnerRecord();
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        $labels = [
            'venta_nueva' => 'Venta Nueva',
            'renovacion' => 'Renovación',
            'renovacion_t1' => 'Renovación T1',
            'renovacion_t2' => 'Renovación T2',
            'renovacion_t3' => 'Renovación T3',
            'adicion' => 'Adición',
            'reporte' => 'Reporte',
        ];

        return $labels[$tipoVenta] ?? ucfirst(str_replace('_', ' ', $tipoVenta));
    }

    private function getMissingDocuments($oportunidad): array
    {
        $documentos = include(config_path('documentos.php'));
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        if (!isset($documentos[$tipoVenta])) {
            return [];
        }

        $obligatorios = collect($documentos[$tipoVenta])
            ->where('obligatorio', true)
            ->pluck('tipo')
            ->toArray();

        $existentes = $oportunidad->documentoOportunidad()
            ->pluck('tipo_documento')
            ->toArray();

        return array_diff($obligatorios, $existentes);
    }

    private function getConditionalDocuments($oportunidad): array
    {
        $documentos = include(config_path('documentos.php'));
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';

        if (!isset($documentos[$tipoVenta])) {
            return [];
        }

        $condicionales = collect($documentos[$tipoVenta])
            ->where('obligatorio', 'condicional')
            ->pluck('tipo')
            ->toArray();

        $existentes = $oportunidad->documentoOportunidad()
            ->pluck('tipo_documento')
            ->toArray();

        return array_intersect($condicionales, $existentes);
    }

    private function getAvailableDocumentTypes(): array
    {
        $oportunidad = $this->getOwnerRecord();
        $tipoVenta = $oportunidad->tipo_venta ?? 'venta_nueva';
        $documentos = include(config_path('documentos.php'));

        if (!isset($documentos[$tipoVenta])) {
            return [];
        }

        $options = [];
        foreach ($documentos[$tipoVenta] as $doc) {
            $options[$doc['tipo']] = $this->formatDocumentLabel($doc['tipo']);
        }

        return $options;
    }

    private function getAllDocumentTypes(): array
    {
        $documentos = include(config_path('documentos.php'));
        $allTypes = [];

        foreach ($documentos as $tipoVenta => $docs) {
            foreach ($docs as $doc) {
                $allTypes[$doc['tipo']] = $this->formatDocumentLabel($doc['tipo']);
            }
        }

        return array_unique($allTypes);
    }

}
