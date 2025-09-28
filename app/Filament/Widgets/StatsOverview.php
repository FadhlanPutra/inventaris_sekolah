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
           Stat::make(
            auth()->user()->hasRole('super_admin')
            ? 'Total Inventory Stock'
            : 'Total Inventory Available Stock'
            , $this->getTotalInventory())
                ->description(
                    auth()->user()->can('view_any_inventory')
                        ? 'Press for details'
                        : 'Showing total available stock'
                )      
                ->descriptionIcon('heroicon-m-cube')
                ->color('primary')
                ->url(
                    auth()->user()->can('view_any_inventory') 
                        ? route('filament.dashboard.resources.inventories.index') 
                        : null
                ),
            Stat::make('Borrowed Items', $this->getBarangDipinjam())
                ->description(
                    auth()->user()->can('view_any_borrow')
                        ? 'Press for details'
                        : 'Currently borrowed items'
                )      
                ->descriptionIcon('heroicon-m-clipboard-document-list')
                ->color('warning')
                ->url(
                    auth()->user()->can('view_any_borrow') 
                        ? route('filament.dashboard.resources.borrows.index') 
                        : null
                ),
            Stat::make('Items Under Maintenance', $this->getMaintenance())
                ->description(
                    auth()->user()->can('view_any_maintenance')
                        ? 'Press for details'
                        : 'Items currently under maintenance'
                )                
                ->descriptionIcon('heroicon-m-wrench')
                ->color('success')
                ->url(
                    auth()->user()->can('view_any_maintenance') 
                        ? route('filament.dashboard.resources.maintenances.index') 
                        : null
                ),
        ];
    }

    private function getTotalInventory(): string
    {
        $query = Inventory::query();

        // Kalau bukan admin, filter hanya status available
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('status', 'Available');
        }

        $total = $query->sum('quantity');

        if ($total >= 1000) {
            return number_format($total / 1000, 1) . 'k';
        }

        return number_format($total);
    }

    private function getBarangDipinjam(): string
    {
        $query = Borrow::query();

        // // Kalau bukan super_admin, filter hanya data milik user sendiri
        // if (!auth()->user()->hasRole('super_admin')) {
        //     $query->where('user_id', auth()->id());
        // }

        // Debug: Cek semua status yang ada di tabel Borrow
        // Uncomment baris ini untuk debug di log
        // \Log::info('Borrow statuses:', Borrow::distinct('status')->pluck('status')->toArray());

        // Coba berbagai kemungkinan nilai status
        $dipinjam = $query->where(function ($q) {
                $q->where('status', 'Pending')
                  ->orWhere('status', 'Active');
            })
            // Tambahan: pastikan belum dikembalikan
            ->where(function ($q) {
                $q->whereNull('return_time')
                  ->orWhere('return_time', '>', now());
            })
            ->sum("quantity");

        // Jika masih 0, coba hitung semua record untuk debug
        if ($dipinjam == 0) {
            $fallbackQuery = Borrow::query();

            // if (!auth()->user()->hasRole('super_admin')) {
            //     $fallbackQuery->where('user_id', auth()->id());
            // }

            $total_borrows = $fallbackQuery->count();
            // Uncomment untuk debug
            // \Log::info("Total Borrow records: $total_borrows");

            // Coba hitung yang belum dikembalikan saja
            $dipinjam = $fallbackQuery->whereNull('return_time')->count();

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
        // $maintenance = Maintenance::where(function ($query) {
        //     $query->where('condition', 'maintenance')
        //           ->orWhere('condition', 'under_maintenance')
        //           ->orWhere('condition', 'broken')
        //           ->orWhere('condition', 'repair')
        //           ->orWhere('condition', 'condition_before')
        //           ->orWhere('condition', 'condition_after');
        // })->count();
        $maintenance = Maintenance::count();

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