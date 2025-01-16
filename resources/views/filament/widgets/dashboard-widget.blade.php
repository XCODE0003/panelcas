<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex  h-full py-1.5 justify-between">
            <div class="text-lg font-bold">Баланс</div>
            <div class="text-lg font-bold px-2 border-bg-gray-200 border rounded-md">{{ auth()->user()->balance }} $</div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
