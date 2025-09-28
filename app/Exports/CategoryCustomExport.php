<?php

namespace App\Exports;

use pxlrbt\FilamentExcel\Columns\Column;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class CategoryCustomExport extends ExcelExport
{
    public function setUp(): void
    {
        $this
            ->withFilename('Category_export_' . now()->format('Y_m_d_H_i_s'))
            // jangan except semua, karena kolom default tetap tapi ditimpa
            // ->except([]) 
            ->withColumns([
                Column::make('id')->heading('ID'),
                Column::make('name')->heading('Name'),
                Column::make('created_at')->heading('Created At'),
                Column::make('updated_at')->heading('Updated At'),
            ]);
    }
}
