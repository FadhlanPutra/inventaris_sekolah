<?php

namespace App\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class InventoryCustomExport extends ExcelExport
{
    public function setUp(): void
    {
        $this
            ->withFilename('Inventory_export_' . now()->format('Y_m_d_H_i_s'))
            // jangan except semua, karena kolom default tetap tapi ditimpa
            // ->except([]) 
            ->withColumns([
                Column::make('id')->heading('ID'),
                Column::make('item_name' )->heading('Item Name'),

                Column::make('category_id')
                    ->heading('Category')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->category 
                            ? $record->category->name 
                            : '-'
                    ),

                Column::make('quantity')->heading('Quantity'),
                Column::make('location_id')
                    ->heading('Location')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->location 
                            ? $record->location->name 
                            : '-'
                    ),
                Column::make('status')->heading('Status'),
                Column::make('desc')->heading('Description'),
                Column::make('created_at')->heading('Created At'),
                Column::make('updated_at')->heading('Updated At'),
            ]);
    }
}
