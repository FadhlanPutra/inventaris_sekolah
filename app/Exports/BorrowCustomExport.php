<?php

namespace App\Exports;

use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BorrowCustomExport extends ExcelExport
{
    public function setUp(): void
    {
        $this
            ->withFilename('Borrows_export_' . now()->format('Y_m_d_H_i_s'))
            // jangan except semua, karena kolom default tetap tapi ditimpa
            // ->except([]) 
            ->withColumns([
                Column::make('id')->heading('ID'),

                Column::make('location_id')
                    ->heading('Location')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->location
                            ? $record->location->name
                            : '-'
                    ),

                Column::make('user_id')
                    ->heading('User')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->user 
                            ? $record->user->name 
                            : '-'
                    ),

                Column::make('item_id')
                    ->heading('Item Name')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->item 
                            ? "". $record->item->item_name 
                            : '-'
                    ),

                Column::make('borrow_time')->heading('Borrow Time'),
                Column::make('return_time')->heading('Return Time'),
                Column::make('quantity')->heading('Quantity'),
                Column::make('status')->heading('Status'),
                Column::make('created_at')->heading('Created At'),
                Column::make('updated_at')->heading('Updated At'),
            ]);
    }
}
