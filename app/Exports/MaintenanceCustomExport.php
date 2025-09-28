<?php

namespace App\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class MaintenanceCustomExport extends ExcelExport
{
    public function setUp(): void
    {
        $this
            ->withFilename('Maintenances_export_' . now()->format('Y_m_d_H_i_s'))
            // jangan except semua, karena kolom default tetap tapi ditimpa
            // ->except([]) 
            ->withColumns([
                Column::make('id')->heading('ID'),

                Column::make('inventory_id')
                    ->heading('Item Name')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->inventory 
                            ? $record->inventory->item_name 
                            : '-'
                    ),

                Column::make('issue')->heading('Issue'),
                Column::make('condition_before')->heading('Condition Before'),
                Column::make('condition_after')->heading('Condition After'),
                Column::make('add_notes')->heading('Notes'),
                Column::make('created_at')->heading('Created At'),
                Column::make('updated_at')->heading('Updated At'),
            ]);
    }
}
