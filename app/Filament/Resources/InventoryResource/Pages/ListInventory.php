<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use Filament\Actions;
use App\Models\Inventory;
use Illuminate\Support\Facades\Log;
use App\Exports\InventoryCustomExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\InventoryResource;
use EightyNine\ExcelImport\ExcelImportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use EightyNine\ExcelImport\Tables\ExcelImportRelationshipAction;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as ExcelExportAction;

class ListInventory extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([

                // Export All (pakai pxlrbt export action)
                ExcelExportAction::make('exportAll')
                    ->label('Export All')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->exports([
                        InventoryCustomExport::make()
                            ->fromModel(Inventory::class)
                            ->modifyQueryUsing(fn ($q) => $q->with('category')),
                    ])
                    ->extraAttributes([
                        // styling item di dropdown: no bg, margin, padding, gap, dll
                        'style' => 'background-color:transparent !important; margin:6px 0 !important; padding:8px 12px !important; border-radius:8px !important; display:flex; align-items:center; gap:8px;'
                    ]),
                
                // Export Last Month
                ExcelExportAction::make('exportLastMonth')
                    ->label('Export Last 1 Month')
                    ->icon('heroicon-o-calendar')
                    ->exports([
                        InventoryCustomExport::make()
                            ->fromModel(Inventory::class)
                            ->modifyQueryUsing(fn ($query) => 
                                $query->with(['category'])
                                      ->where('created_at', '>=', now()->subMonth())
                            ),
                    ])
                    ->extraAttributes([
                        'style' => 'background-color:transparent !important; margin:6px 0 !important; padding:8px 12px !important; border-radius:8px !important; display:flex; align-items:center; gap:8px;'
                    ]),
        ])
        ->label('Export')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('primary')  // tombol utama biru
        ->button()
        ->visible(auth()->user()->can('export_inventory'))
        ->outlined(false),
        ExcelImportAction::make()
            ->slideOver()
            ->visible(auth()->user()->can('import_inventory'))
            ->authorize(fn () => auth()->user()->can('import_inventory'))
            ->icon('heroicon-o-arrow-up-tray')
            ->sampleExcel(
                sampleData: [
                    ['Item Name' => 'Laptop', 'Category' => 'Electronics', 'Quantity' => '12', 'Status' => 'Available', 'Description' => 'High-end laptop'],
                    ['Item Name' => 'Office Chair', 'Category' => 'Furniture', 'Quantity' => '5', 'Status' => 'Unavailable', 'Description' => 'Ergonomic chair'],
                    ['Item Name' => 'Projector', 'Category' => 'Electronics', 'Quantity' => '3', 'Status' => 'Available', 'Description' => '4K projector'],
                    ['Item Name' => 'Desk Lamp', 'Category' => 'Furniture', 'Quantity' => '20', 'Status' => 'Available', 'Description' => 'LED desk lamp'],
                    ['Item Name' => 'Whiteboard', 'Category' => 'Office Supplies', 'Quantity' => '7', 'Status' => 'Unavailable', 'Description' => 'Magnetic whiteboard'],
                ], 
                fileName: 'sample_inventories.xlsx', 
                sampleButtonLabel: 'Download Sample',
            )
            ->modalIcon('heroicon-o-arrow-up-tray')
            ->mutateBeforeValidationUsing(function (array $data): array {
                // Debug: Log semua data yang masuk
                // Log::info('=== DEBUG IMPORT DATA ===');
                // Log::info('All data keys:', array_keys($data));
                // Log::info('All data values:', $data);

                // Coba berbagai kemungkinan nama field untuk category
                $raw = $data['category'] ?? $data['Category'] ?? $data['category_id'] ?? $data['Category_ID'] ?? $data['kategori'] ?? $data['Kategori'] ?? null;

                // Log::info('Raw category data:', ['raw' => $raw, 'type' => gettype($raw)]);
            
                if ($raw && trim($raw) !== '') {
                    $raw = trim($raw);

                    // Jika berupa angka, cek apakah ada di tabel category sebagai id
                    if (is_numeric($raw)) {
                        $category = \App\Models\Category::find((int) $raw);

                        if (!$category) {
                            // Jika tidak ada, buat category baru dengan nama berdasarkan id
                            try {
                                $category = \App\Models\Category::create([
                                    'name' => $raw,
                                ]);
                                // Log::info("Category baru dibuat dengan ID: {$category->id}, nama: {$category->name}");
                            } catch (\Exception $e) {
                                Log::error("Gagal membuat category baru: " . $e->getMessage());
                                throw new \Exception("Gagal membuat category baru: " . $e->getMessage());
                            }
                        } else {
                            // Log::info("Category ditemukan dengan ID: {$category->id}, nama: {$category->name}");
                        }
                    } else {
                        // Jika berupa string, cari berdasarkan nama (case-insensitive)
                        $category = \App\Models\Category::whereRaw(
                            'LOWER(name) = ?',
                            [strtolower($raw)]
                        )->first();
                        
                        if (!$category) {
                            // Jika tidak ada, buat category baru
                            try {
                                $category = \App\Models\Category::create([
                                    'name' => $raw,
                                ]);
                                // Log::info("Category baru dibuat dengan ID: {$category->id}, nama: {$category->name}");
                            } catch (\Exception $e) {
                                Log::error("Gagal membuat category baru: " . $e->getMessage());
                                throw new \Exception("Gagal membuat category baru: " . $e->getMessage());
                            }
                        } else {
                            // Log::info("Category ditemukan dengan ID: {$category->id}, nama: {$category->name}");
                        }
                    }
                
                    $data['category_id'] = $category->id;
                    Log::info("Category ID berhasil di-set: {$category->id}");
                } else {
                    // Jika category kosong atau null
                    Log::warning('Category kosong atau null di row import', $data);
                    throw new \Exception("Category tidak boleh kosong. Silakan isi kolom category dengan nama kategori atau ID kategori yang valid.");
                }
            
                // Perbaiki field lain yang mungkin tidak sesuai
                if (isset($data['quantity']) && empty($data['quantity'])) {
                    $data['quantity'] = 1; // Default quantity jika kosong
                }

                // Handle status yang mungkin mengandung quantity
                if (isset($data['status']) && preg_match('/(\d+)\s+(.+)/', $data['status'], $matches)) {
                    $data['quantity'] = (int) $matches[1];
                    $data['status'] = trim($matches[2]);
                }

                // Normalisasi status - ubah ke format yang benar
                if (isset($data['status'])) {
                    $status = strtolower(trim($data['status']));
                    if ($status === 'available') {
                        $data['status'] = 'Available';
                    } elseif ($status === 'unavailable') {
                        $data['status'] = 'Unavailable';
                    }
                    // Log::info("Status dinormalisasi: '{$data['status']}'");
                }
            
                // Mapping langsung ke field model yang benar
                $finalData = [
                    'item_name' => $data['item_name'] ?? '',
                    'category_id' => $data['category_id'] ?? null,
                    'quantity' => $data['quantity'] ?? 1,
                    'status' => $data['status'] ?? 'Available',
                    'desc' => $data['description'] ?? $data['desc'] ?? '',
                ];

                // Debug: Pastikan category_id tidak null
                if (!$finalData['category_id']) {
                    Log::error('Category ID masih null!', ['finalData' => $finalData, 'originalData' => $data]);
                    throw new \Exception("Category ID tidak boleh null. Pastikan category sudah di-set dengan benar.");
                }

                // Log::info('Final data yang akan disimpan:', $finalData);
                // Log::info('=== END DEBUG IMPORT DATA ===');
                return $data; // Return data asli, bukan finalData
            })
            ->mutateAfterValidationUsing(function (array $data): array {
                // Log::info('=== AFTER VALIDATION MUTATION ===');
                // Log::info('Data setelah validasi:', $data);

                // Resolve category lagi karena data mungkin di-reset
                $raw = $data['category'] ?? $data['Category'] ?? null;

                if ($raw && trim($raw) !== '') {
                    $raw = trim($raw);

                    if (is_numeric($raw)) {
                        $category = \App\Models\Category::find((int) $raw);
                        if (!$category) {
                            $category = \App\Models\Category::create(['name' => $raw]);
                        }
                    } else {
                        $category = \App\Models\Category::whereRaw('LOWER(name) = ?', [strtolower($raw)])->first();
                        if (!$category) {
                            $category = \App\Models\Category::create(['name' => $raw]);
                        }
                    }

                    $data['category_id'] = $category->id;
                    // Log::info("Category ID di-set ulang: {$category->id}");
                }

                // Normalisasi status lagi untuk memastikan konsistensi
                $status = $data['status'] ?? 'Available';
                if (is_string($status)) {
                    $statusLower = strtolower(trim($status));
                    if ($statusLower === 'available') {
                        $status = 'Available';
                    } elseif ($statusLower === 'unavailable') {
                        $status = 'Unavailable';
                    }
                }

                // Mapping ke field model
                $mappedData = [
                    'item_name' => $data['item_name'] ?? '',
                    'category_id' => $data['category_id'] ?? null,
                    'quantity' => $data['quantity'] ?? 1,
                    'status' => $status,
                    'desc' => $data['description'] ?? $data['desc'] ?? '',
                ];

                // Log::info('Data yang sudah di-mapping:', $mappedData);
                // Log::info('=== END AFTER VALIDATION MUTATION ===');

                return $mappedData;
            })
            ->validateUsing([
                'item_name' => 'required|unique:inventories,item_name',
                'category_id' => 'required|exists:categories,id',
                'quantity' => 'required|integer|min:1',
                'status' => 'required|in:Available,Unavailable',
                'desc' => 'nullable',
            ])
            ->color('primary'),
            Actions\CreateAction::make(),
        ];
    }
}