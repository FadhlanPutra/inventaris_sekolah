<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\LabUsage;
use Carbon\Carbon;

class BlogPostsChart extends ChartWidget
{
    protected static ?string $heading = 'Lab Usage Statistics';
    protected static ?int $sort = 2;
    
    protected function getData(): array
    {
         $data = $this->getLabUsageDataPerMonth();
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman Lab',
                    'data' => $data['counts'],
                    'borderColor' => 'rgb(59, 130, 246)', // Warna biru
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4, // Membuat garis lebih smooth
                    'fill' => true, // Mengisi area di bawah garis
                ],
            ],
            'labels' => $data['labels'], // Label bulan
        ];
    }
    

     private function getLabUsageDataPerMonth(): array
    {
        $months = [];
        $counts = [];
        
        // Loop 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $monthName = $date->format('M'); // Jan, Feb, Mar, dst
            
            // Hitung jumlah peminjaman lab pada bulan tersebut
            $count = LabUsage::whereYear('created_at', $date->year)
                            ->whereMonth('created_at', $date->month)
                            ->count();
            
            $months[] = $monthName;
            $counts[] = $count;
        }
        
        return [
            'labels' => $months,
            'counts' => $counts
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}