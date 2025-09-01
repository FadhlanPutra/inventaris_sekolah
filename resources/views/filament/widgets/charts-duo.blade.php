<x-filament-widgets::widget>
    {{-- <x-filament::section> --}}
        <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-2 gap-4 duo-charts">
            <div class="chart-1">
                @livewire(\App\Filament\Widgets\BlogPostsChart::class)
            </div>
            
            <div class="chart-2">
                @livewire(\App\Filament\Widgets\BlogPosts2Chart::class)
            </div>
        </div>
    {{-- </x-filament::section> --}}
</x-filament-widgets::widget>