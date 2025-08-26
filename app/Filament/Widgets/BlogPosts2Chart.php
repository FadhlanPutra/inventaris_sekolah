<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Borrow;
use Carbon\Carbon;

class BlogPosts2Chart extends ChartWidget
{
    protected static ?string $heading = 'Borrowed Items Statistics';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $data = $this->getBorrowDataPerMonth();
        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Peminjaman Barang',
                    'data' => $data['counts'],
                    'borderColor' => 'rgb(34, 197, 94)', // Warna hijau
                    'backgroundColor' => 'rgba(34, 197, 94, 0.1)',
                    'tension' => 0.4, // Membuat garis lebih smooth
                    'fill' => true, // Mengisi area di bawah garis
                ],
            ],
            'labels' => $data['labels'], // Label bulan
        ];
    }
    
    private function getBorrowDataPerMonth(): array
    {
        $months = [];
        $counts = [];
        
        // Loop 12 bulan terakhir
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $monthYear = $date->format('Y-m');
            $monthName = $date->format('M'); // Jan, Feb, Mar, dst
            
            // Hitung jumlah peminjaman barang pada bulan tersebut
            $count = Borrow::whereYear('borrow_time', $date->year)
                          ->whereMonth('borrow_time', $date->month)
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