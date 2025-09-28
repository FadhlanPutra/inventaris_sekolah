<?php

namespace App\Exports;

use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Columns\Column;

class LabUsageCustomExport extends ExcelExport
{
    public function setUp(): void
    {
        $this
            ->withFilename('LabUsage_export_' . now()->format('Y_m_d_H_i_s'))
            // jangan except semua, karena kolom default tetap tapi ditimpa
            // ->except([]) 
            ->withColumns([
                Column::make('id')->heading('ID'),

                Column::make('user_id')
                    ->heading('Username')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->user 
                            ? $record->user->name 
                            : '-'
                    ),

                Column::make('status')->heading('Status'),
                Column::make('num_lab')->heading('Lab'),
                Column::make('class_name')->heading('Class Name'),
                Column::make('num_students')->heading('Students'),
                Column::make('lab_function')
                    ->heading('Lab Function')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->num_lab 
                            ? "Lab " . $record->num_lab
                            : '-'
                    ),
                Column::make('end_state')->heading('End State'),
                Column::make('notes')->heading('Notes'),
                Column::make('created_at')->heading('Created At'),
                Column::make('updated_at')->heading('Updated At'),
            ]);
    }
}
