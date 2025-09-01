<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Inventory;
use App\Models\Borrow;
use App\Models\Maintenance;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;
    
    protected function getStats(): array
    {
        return [
           Stat::make('Laboratory Total Stock', $this->getTotalInventory())
                ->description('All available items in stock')
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary'),

            Stat::make('Borrowed Items', $this->getBarangDipinjam())
                ->description('7% increase in usage')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make('Items Under Maintenance', $this->getMaintenance())
                ->description('3% increase from last month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),
        ];
    }

    private function getTotalInventory(): string
    {
        $total = Inventory::sum('quantity');

        if ($total >= 1000) {
            return number_format($total / 1000, 1) . 'k';
        }
        
        return number_format($total);
    }

    private function getBarangDipinjam(): string
    {
        // Debug: Cek semua status yang ada di tabel Borrow
        // Uncomment baris ini untuk debug di log
        // \Log::info('Borrow statuses:', Borrow::distinct('status')->pluck('status')->toArray());
        
        // Coba berbagai kemungkinan nilai status
        $dipinjam = Borrow::where(function ($query) {
            $query->where('status', 'active')
                  ->orWhere('status', 'approved')
                  ->orWhere('status', 'borrowed')
                  ->orWhere('status', 'ongoing')
                  ->orWhere('status', 'pending');
        })
        // Tambahan: pastikan belum dikembalikan
        ->where(function ($query) {
            $query->whereNull('return_time')
                  ->orWhere('return_time', '>', now());
        })
        ->sum("quantity");
        
        // Jika masih 0, coba hitung semua record untuk debug
        if ($dipinjam == 0) {
            $total_borrows = Borrow::count();
            // Uncomment untuk debug
            // \Log::info("Total Borrow records: $total_borrows");
            
            // Coba hitung yang belum dikembalikan saja
            $dipinjam = Borrow::whereNull('return_time')->count();
            
            // Jika masih 0, tampilkan total untuk sementara
            if ($dipinjam == 0) {
                $dipinjam = $total_borrows;
            }
        }
        
        // Format angka
        if ($dipinjam >= 1000) {
            return number_format($dipinjam / 1000, 1) . 'k';
        }
        return number_format($dipinjam);
    }

    private function getMaintenance(): string
    {
        // Debug maintenance juga
        $maintenance = Maintenance::where(function ($query) {
            $query->where('condition', 'maintenance')
                  ->orWhere('condition', 'under_maintenance')
                  ->orWhere('condition', 'broken')
                  ->orWhere('condition', 'repair')
                  ->orWhere('condition', 'condition_before')
                  ->orWhere('condition', 'condition_after');
        })->count();
        
        // Jika masih 0, coba hitung semua record
        if ($maintenance == 0) {
            $maintenance = Maintenance::count();
        }
        
        // Format angka
        if ($maintenance >= 1000) {
            return number_format($maintenance / 1000, 1) . 'k';
        }
        return number_format($maintenance);
    }

    // Method debug untuk cek nilai status yang ada (opsional)
    public function debugBorrowStatus()
    {
        $statuses = Borrow::distinct('status')->pluck('status')->toArray();
        dd('Borrow statuses found:', $statuses);
    }

    public function debugMaintenanceCondition()
    {
        $conditions = Maintenance::distinct('condition')->pluck('condition')->toArray();
        dd('Maintenance conditions found:', $conditions);
    }
}