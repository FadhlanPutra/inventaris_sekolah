<?php

namespace App\Filament\Dashboard\Themes;

use Filament\Panel;
use Hasnayeen\Themes\Contracts\CanModifyPanelConfig;
use Hasnayeen\Themes\Contracts\HasChangeableColor;
use Hasnayeen\Themes\Contracts\Theme;
use Illuminate\Support\Arr;
use Filament\Support\Colors\Color;

class Pesat implements CanModifyPanelConfig, Theme, HasChangeableColor
{
    public static function getName(): string
    {
        return 'Pesat';
    }

    public static function getPath(): string
    {
        return 'resources/css/filament/dashboard/themes/pesat.css';
    }

    public function getThemeColor(): array
    {
        // return [
        //     'primary' => '#9580ff',
        //     'secondary' => '#ff80bf',
        //     'custom' => '#6932f5',
        //     'info' => '#80ffea',
        //     'success' => '#8aff80',
        //     'warning' => '#f9f06b',
        //     'danger' => '#ff9580',
        // ];

        return Arr::except(Color::all(), ['gray', 'zinc', 'neutral', 'stone']);
    }

     public function getPrimaryColor(): array
    {
        return ['primary' => $this->getThemeColor()['orange']];
    }

    public function modifyPanelConfig(Panel $panel): Panel
    {
        return $panel
            ->viteTheme($this->getPath());
        }
}
