<div class="space-y-4">
    {{-- Contenedor de Pestañas --}}
    <x-filament::tabs label="Categorías de Renovación">
        {{-- Pestaña: Todas --}}
        <x-filament::tabs.item
            :active="$activeTab === 'all'"
            wire:click="setActiveTab('all')"
            :badge="$this->getAllCount()"
        >
            Todas
        </x-filament::tabs.item>

        {{-- Pestaña: Vencidas (Solo se muestra si hay alguna) --}}
        @if($this->getVencidasCount() > 0)
            <x-filament::tabs.item
                :active="$activeTab === 'vencidas'"
                wire:click="setActiveTab('vencidas')"
                :badge="$this->getVencidasCount()"
                icon="heroicon-m-exclamation-triangle"
                badge-color="danger"
            >
                Vencidas
            </x-filament::tabs.item>
        @endif

        {{-- Pestaña: T1 (Solo se muestra si hay alguna) --}}
        @if($this->getT1Count() > 0)
            <x-filament::tabs.item
                :active="$activeTab === 't1'"
                wire:click="setActiveTab('t1')"
                :badge="$this->getT1Count()"
                icon="heroicon-m-clock"
                badge-color="warning"
            >
                Próximos 30 días
            </x-filament::tabs.item>
        @endif

        {{-- Pestaña: T2 (Solo se muestra si hay alguna) --}}
        @if($this->getT2Count() > 0)
            <x-filament::tabs.item
                :active="$activeTab === 't2'"
                wire:click="setActiveTab('t2')"
                :badge="$this->getT2Count()"
                badge-color="info"
            >
                31-60 días
            </x-filament::tabs.item>
        @endif

        {{-- Pestaña: T3 (Solo se muestra si hay alguna) --}}
        @if($this->getT3Count() > 0)
            <x-filament::tabs.item
                :active="$activeTab === 't3'"
                wire:click="setActiveTab('t3')"
                :badge="$this->getT3Count()"
                badge-color="success"
            >
                61-90 días
            </x-filament::tabs.item>
        @endif
    </x-filament::tabs>

    {{-- La tabla se renderizará aquí y cambiará según la pestaña activa --}}
    {{ $this->table }}
</div>
