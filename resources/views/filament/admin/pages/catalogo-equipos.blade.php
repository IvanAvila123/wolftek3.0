<x-filament-panels::page>
    {{-- Header con título y estadísticas --}}
    <div class="fi-page-header mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            {{-- Contador de equipos --}}
            <div class="flex gap-4">
                <div class="fi-badge-group gap-1">
                    <span class="fi-badge flex items-center justify-center gap-1 rounded-md text-xs font-medium ring-1 ring-inset px-3 py-1.5 bg-primary-50 text-primary-600 ring-primary-600/10 dark:bg-primary-400/10 dark:text-primary-400 dark:ring-primary-400/30">
                        <x-heroicon-m-device-phone-mobile class="h-4 w-4" />
                        <span class="font-semibold">{{ $equipos->count() }}</span>
                        {{ str('Equipo')->plural($equipos->count()) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Filtros por Sistema Operativo --}}
    @php
        $sistemasOperativos = $equipos->pluck('sistema_operativo')->unique()->filter();
    @endphp

    @if($sistemasOperativos->count() > 1)
        <div class="fi-section-content-ctn mb-6">
            <div class="flex flex-wrap gap-2">
                <button class="fi-badge flex items-center justify-center gap-1 rounded-md text-xs font-medium ring-1 ring-inset px-3 py-1.5 bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30 hover:bg-gray-100 dark:hover:bg-gray-400/20 transition-colors cursor-pointer">
                    Todos
                </button>
                @foreach($sistemasOperativos as $so)
                    <button class="fi-badge flex items-center justify-center gap-1 rounded-md text-xs font-medium ring-1 ring-inset px-3 py-1.5 bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/30 hover:bg-primary-50 hover:text-primary-600 hover:ring-primary-600/20 dark:hover:bg-primary-400/20 dark:hover:text-primary-400 transition-colors cursor-pointer">
                        @if(str_contains(strtolower($so), 'android'))
                            <x-heroicon-m-device-phone-mobile class="h-3 w-3" />
                        @elseif(str_contains(strtolower($so), 'ios'))
                            <x-heroicon-m-device-phone-mobile class="h-3 w-3" />
                        @else
                            <x-heroicon-m-cpu-chip class="h-3 w-3" />
                        @endif
                        {{ $so }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Grid de equipos con diseño Filament --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

        @forelse ($equipos as $equipo)
            {{-- Card estilo Filament --}}
            <div class="fi-ta-table rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 overflow-hidden transition-all duration-250 hover:shadow-lg hover:-translate-y-1 hover:ring-primary-600/20 dark:hover:ring-primary-400/30">
                <a href="{{ \App\Filament\Admin\Resources\Equipos\EquipoResource::getUrl('edit', ['record' => $equipo]) }}"
                   class="block group">

                    {{-- Imagen del equipo con aspect ratio consistente --}}
                    <div class="relative aspect-[4/5] bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-800/50 dark:to-gray-900/50 overflow-hidden">
                        @if ($equipo->foto_principal)
                            <img src="{{ Storage::url($equipo->foto_principal) }}"
                                 alt="{{ $equipo->nombre }}"
                                 loading="lazy"
                                 class="h-full w-full object-contain p-4 transition-transform duration-500 group-hover:scale-110">
                        @else
                            <div class="flex h-full w-full items-center justify-center">
                                <x-heroicon-o-device-phone-mobile class="h-20 w-20 text-gray-300 dark:text-gray-700" />
                            </div>
                        @endif

                        {{-- Badge del Sistema Operativo --}}
                        @if($equipo->sistema_operativo)
                            <div class="absolute top-3 left-3">
                                @php
                                    $soLower = strtolower($equipo->sistema_operativo);
                                    $soBadgeClass = 'bg-gray-50 text-gray-600 ring-gray-600/10 dark:bg-gray-400/10 dark:text-gray-400';

                                    if(str_contains($soLower, 'android')) {
                                        $soBadgeClass = 'bg-green-50 text-green-600 ring-green-600/10 dark:bg-green-400/10 dark:text-green-400';
                                    } elseif(str_contains($soLower, 'ios') || str_contains($soLower, 'iphone')) {
                                        $soBadgeClass = 'bg-gray-900 text-white ring-gray-900/10 dark:bg-gray-100 dark:text-gray-900';
                                    }
                                @endphp
                                <span class="fi-badge inline-flex items-center gap-1 rounded-full text-xs font-medium ring-1 ring-inset px-2 py-0.5 {{ $soBadgeClass }}">
                                    {{ $equipo->sistema_operativo }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Contenido de la card --}}
                    <div class="p-4 space-y-3">
                        {{-- Nombre del equipo --}}
                        <div>
                            <h3 class="fi-ta-text-item-label text-base font-semibold text-gray-950 dark:text-white line-clamp-2 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors"
                                title="{{ $equipo->nombre }}">
                                {{ $equipo->nombre }}
                            </h3>
                        </div>

                        {{-- Especificaciones técnicas en grid --}}
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            {{-- Pantalla --}}
                            @if($equipo->pantalla)
                                <div class="flex items-start gap-1.5">
                                    <x-heroicon-m-device-tablet class="h-3.5 w-3.5 text-gray-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Pantalla</span>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $equipo->pantalla }}"</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Capacidad --}}
                            @if($equipo->capacidad)
                                <div class="flex items-start gap-1.5">
                                    <x-heroicon-m-circle-stack class="h-3.5 w-3.5 text-gray-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Almacenamiento</span>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $equipo->capacidad }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Cámara --}}
                            @if($equipo->camara)
                                <div class="flex items-start gap-1.5">
                                    <x-heroicon-m-camera class="h-3.5 w-3.5 text-gray-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Cámara</span>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $equipo->camara }}</p>
                                    </div>
                                </div>
                            @endif

                            {{-- Resolución --}}
                            @if($equipo->resolucion)
                                <div class="flex items-start gap-1.5">
                                    <x-heroicon-m-computer-desktop class="h-3.5 w-3.5 text-gray-400 flex-shrink-0 mt-0.5" />
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Resolución</span>
                                        <p class="font-medium text-gray-900 dark:text-white text-xs">{{ $equipo->resolucion }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Footer con acción --}}
                        <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    ID: #{{ str_pad($equipo->id, 4, '0', STR_PAD_LEFT) }}
                                </span>

                                <span class="fi-link group/link inline-flex items-center gap-1 text-xs font-medium text-gray-600 dark:text-gray-400 group-hover:text-primary-600 dark:group-hover:text-primary-400">
                                    Ver detalles
                                    <x-heroicon-m-arrow-right class="h-3 w-3 transition-transform group-hover:translate-x-0.5" />
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            </div>

        @empty
            {{-- Estado vacío estilo Filament --}}
            <div class="col-span-full">
                <div class="fi-ta-empty-state mx-auto max-w-lg py-16">
                    <div class="fi-ta-empty-state-content mx-auto max-w-md space-y-6 text-center">
                        <div class="fi-ta-empty-state-icon-ctn flex items-center justify-center">
                            <div class="rounded-full bg-gray-100 p-4 dark:bg-gray-800">
                                <x-heroicon-o-device-phone-mobile class="h-8 w-8 text-gray-500 dark:text-gray-400" />
                            </div>
                        </div>

                        <div class="space-y-2">
                            <h4 class="fi-ta-empty-state-heading text-base font-semibold text-gray-950 dark:text-white">
                                No hay equipos en el catálogo
                            </h4>

                            <p class="fi-ta-empty-state-description text-sm text-gray-600 dark:text-gray-400">
                                Comienza agregando tu primer equipo móvil al inventario.
                            </p>
                        </div>

                        @if(\App\Filament\Admin\Resources\Equipos\EquipoResource::canCreate())
                            <div class="fi-ta-actions flex flex-col gap-3 sm:flex-row sm:justify-center">
                                <a href="{{ \App\Filament\Admin\Resources\Equipos\EquipoResource::getUrl('create') }}"
                                   class="fi-btn fi-btn-size-md fi-btn-color-primary inline-flex items-center justify-center gap-1 rounded-lg px-4 py-2.5 text-sm font-semibold text-white shadow-sm bg-primary-600 hover:bg-primary-500 dark:bg-primary-500 dark:hover:bg-primary-400 focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors">
                                    <x-heroicon-m-plus-circle class="h-5 w-5" />
                                    <span>Agregar Primer Equipo</span>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforelse

    </div>

    {{-- Paginación si existe --}}
    @if($equipos instanceof \Illuminate\Contracts\Pagination\Paginator && $equipos->hasPages())
        <div class="fi-ta-pagination-ctn mt-6">
            {{ $equipos->links() }}
        </div>
    @endif

    {{-- Botón flotante para agregar (cuando hay equipos) --}}
    @if($equipos->count() > 0 && \App\Filament\Admin\Resources\Equipos\EquipoResource::canCreate())
        <div class="fixed bottom-6 right-6 z-40">
            <a href="{{ \App\Filament\Admin\Resources\Equipos\EquipoResource::getUrl('create') }}"
               title="Agregar nuevo equipo"
               class="fi-btn-floating group flex h-14 w-14 items-center justify-center rounded-full bg-primary-600 text-white shadow-lg hover:bg-primary-500 hover:scale-110 hover:shadow-xl transition-all duration-200">
                <x-heroicon-m-plus class="h-6 w-6" />
                <span class="sr-only">Agregar equipo</span>
            </a>
        </div>
    @endif
</x-filament-panels::page>
