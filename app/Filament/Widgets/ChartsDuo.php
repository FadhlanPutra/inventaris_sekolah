<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class ChartsDuo extends Widget
{
    protected static ?string $heading = 'Analytics Overview';
    protected static string $view = 'filament.widgets.charts-duo';
    protected int|string|array $columnSpan = 'full';
}
