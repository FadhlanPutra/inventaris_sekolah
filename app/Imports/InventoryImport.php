<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Category;
use App\Models\Location;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class InventoryImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError, SkipsOnFailure, WithBatchInserts, WithChunkReading
{
    use Importable, SkipsErrors, SkipsFailures;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        Log::info('Processing row:', $row);

        // Handle category_id & location_id
        $categoryId = $this->resolveCategoryId($row);
        $locationId = $this->resolveLocationId($row);

        if (!$categoryId) {
            Log::error('Category ID tidak dapat diselesaikan untuk row:', $row);
            throw new \Exception("Category tidak valid untuk item: " . ($row['item_name'] ?? 'Unknown'));
        } elseif (!$locationId) { // ✅ fix disini
            Log::error('Location ID tidak dapat diselesaikan untuk row:', $row);
            throw new \Exception("Location tidak valid untuk item: " . ($row['item_name'] ?? 'Unknown'));
        }

        $inventory = new Inventory([
            'item_name'   => $row['item_name'] ?? '',
            'category_id' => $categoryId,
            'location_id' => $locationId,
            'quantity'    => $row['quantity'] ?? 0,
            'status'      => $row['status'] ?? 'Available',
            'desc'        => $row['desc'] ?? $row['description'] ?? '',
        ]);

        Log::info('Model Inventory saving:', $inventory->toArray());

        return $inventory;
    }

    /**
     * Resolve category ID
     */
    private function resolveCategoryId(array $row): ?int
    {
        $raw = $row['category'] ?? $row['Category'] ?? $row['category_id'] ?? $row['Category_ID'] ?? null;

        Log::info('Raw category data:', ['raw' => $raw, 'row' => $row]);

        if (!$raw || trim($raw) === '') {
            Log::warning('Category kosong atau null');
            return null;
        }

        $raw = trim($raw);

        if (is_numeric($raw)) {
            $category = Category::find((int) $raw);
            if (!$category) {
                try {
                    $category = Category::create(['name' => "Kategori $raw"]);
                    Log::info("Category baru dibuat dengan ID: {$category->id}, nama: {$category->name}");
                } catch (\Exception $e) {
                    Log::error("Gagal membuat category baru: " . $e->getMessage());
                    return null;
                }
            }
        } else {
            $category = Category::whereRaw('LOWER(name) = ?', [strtolower($raw)])->first();
            if (!$category) {
                try {
                    $category = Category::create(['name' => $raw]);
                    Log::info("Category baru dibuat dengan ID: {$category->id}, nama: {$category->name}");
                } catch (\Exception $e) {
                    Log::error("Gagal membuat category baru: " . $e->getMessage());
                    return null;
                }
            }
        }

        Log::info("Category ID berhasil di-set: {$category->id}");
        return $category->id;
    }

    /**
     * Resolve location ID
     */
    private function resolveLocationId(array $row): ?int
    {
        $raw = $row['location'] ?? $row['Location'] ?? $row['location_id'] ?? $row['Location_ID'] ?? null;

        Log::info('Raw location data:', ['raw' => $raw, 'row' => $row]);

        if (!$raw || trim($raw) === '') {
            Log::warning('Location kosong atau null');
            return null;
        }

        $raw = trim($raw);

        if (is_numeric($raw)) {
            $location = Location::find((int) $raw);
            if (!$location) {
                try {
                    $location = Location::create(['name' => "Lokasi $raw"]);
                    Log::info("Location baru dibuat dengan ID: {$location->id}, nama: {$location->name}");
                } catch (\Exception $e) {
                    Log::error("Gagal membuat location baru: " . $e->getMessage());
                    return null;
                }
            }
        } else {
            $location = Location::whereRaw('LOWER(name) = ?', [strtolower($raw)])->first();
            if (!$location) {
                try {
                    $location = Location::create(['name' => $raw]);
                    Log::info("Location baru dibuat dengan ID: {$location->id}, nama: {$location->name}");
                } catch (\Exception $e) {
                    Log::error("Gagal membuat location baru: " . $e->getMessage());
                    return null;
                }
            }
        }

        Log::info("Location ID berhasil di-set: {$location->id}");
        return $location->id;
    }

    /**
     * Validation rules
     */
    public function rules(): array
    {
        return [
            'item_name' => 'required|string|max:255',
            'quantity'  => 'required|integer|min:0',
            'status'    => 'nullable|in:Available,Unavailable',
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'item_name.required' => 'Nama item wajib diisi',
            'item_name.string'   => 'Nama item harus berupa teks',
            'item_name.max'      => 'Nama item maksimal 255 karakter',
            'quantity.required'  => 'Jumlah wajib diisi',
            'quantity.integer'   => 'Jumlah harus berupa angka',
            'quantity.min'       => 'Jumlah minimal 0',
            'status.in'          => 'Status harus Available atau Unavailable',
        ];
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
