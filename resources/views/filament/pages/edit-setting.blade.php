<x-filament-panels::page>
    <x-slot name="header">
        <h1 class="text-2xl font-bold">Настройки</h1>
    </x-slot>
   
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-3">
            <x-filament::button type="submit">
                Сохранить
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
