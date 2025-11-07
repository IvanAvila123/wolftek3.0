<x-filament-panels::page>
    {{-- Stats Cards Grid --}}

    {{-- Tabs Navigation --}}
    <div class="mb-6">

        <x-filament::tabs>

            <x-filament::tabs.item :active="$activeTab === 'all'" wire:click="setActiveTab('all')"
                icon="heroicon-o-circle-stack" badge="{{ $this->getAllCount() }}" badge-color="primary">
                Todas Las Lineas
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 'vencidas'" wire:click="setActiveTab('vencidas')"
                icon="heroicon-o-exclamation-circle" badge="{{ $this->getVencidasCount() }}" badge-color="danger">
                Vencidas
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 't1'" wire:click="setActiveTab('t1')" icon="heroicon-o-fire"
                badge="{{ $this->getT1Count() }}" badge-color="warning">
                T1
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 't2'" wire:click="setActiveTab('t2')" icon="heroicon-o-clock"
                badge="{{ $this->getT2Count() }}" badge-color="info">
                T2
            </x-filament::tabs.item>

            <x-filament::tabs.item :active="$activeTab === 't3'" wire:click="setActiveTab('t3')"
                icon="heroicon-o-check-circle" badge="{{ $this->getT3Count() }}" badge-color="success">
                T3
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    {{-- Tabla --}}
    {{ $this->table }}
</x-filament-panels::page>
