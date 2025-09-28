<?php

namespace App\Imports;

use App\Models\Inventory;
use App\Models\Category;
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

        // Handle category_id
        $categoryId = $this->resolveCategoryId($row);
        
        if (!$categoryId) {
            Log::error('Category ID tidak dapat diselesaikan untuk row:', $row);
            throw new \Exception("Category tidak valid untuk item: " . ($row['item_name'] ?? 'Unknown'));
        }

        return new Inventory([
            'item_name'   => $row['item_name'] ?? $row['item_name'] ?? '',
            'category_id' => $categoryId,
            'quantity'    => $row['quantity'] ?? $row['quantity'] ?? 0,
            'status'      => $row['status'] ?? $row['status'] ?? 'Available',
            'desc'        => $row['desc'] ?? $row['description'] ?? $row['desc'] ?? '',
        ]);
    }

    /**
     * Resolve category ID from various possible formats
     */
    private function resolveCategoryId(array $row): ?int
    {
        // Coba berbagai kemungkinan nama field untuk category
        $raw = $row['category'] ?? $row['Category'] ?? $row['category_id'] ?? $row['Category_ID'] ?? null;
        
        Log::info('Raw category data:', ['raw' => $raw, 'row' => $row]);

        if (!$raw || trim($raw) === '') {
            Log::warning('Category kosong atau null');
            return null;
        }

        $raw = trim($raw);
        
        // Jika berupa angka, cek apakah ada di tabel category sebagai id
        if (is_numeric($raw)) {
            $category = Category::find((int) $raw);
            
            if (!$category) {
                // Jika tidak ada, buat category baru dengan nama berdasarkan id
                try {
                    $category = Category::create([
                        'name' => "Kategori $raw",
                    ]);
                    Log::info("Category baru dibuat dengan ID: {$category->id}, nama: {$category->name}");
                } catch (\Exception $e) {
                    Log::error("Gagal membuat category baru: " . $e->getMessage());
                    return null;
                }
            } else {
                Log::info("Category ditemukan dengan ID: {$category->id}, nama: {$category->name}");
            }
        } else {
            // Jika berupa string, cari berdasarkan nama (case-insensitive)
            $category = Category::whereRaw(
                'LOWER(name) = ?',
                [strtolower($raw)]
            )->first();

            if (!$category) {
                // Jika tidak ada, buat category baru
                try {
                    $category = Category::create([
                        'name' => $raw,
                    ]);
                    Log::info("Category baru dibuat dengan ID: {$category->id}, nama: {$category->name}");
                } catch (\Exception $e) {
                    Log::error("Gagal membuat category baru: " . $e->getMessage());
                    return null;
                }
            } else {
                Log::info("Category ditemukan dengan ID: {$category->id}, nama: {$category->name}");
            }
        }

        return $category->id;
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

    /**
     * Batch size for inserts
     */
    public function batchSize(): int
    {
        return 100;
    }

    /**
     * Chunk size for reading
     */
    public function chunkSize(): int
    {
        return 100;
    }
}