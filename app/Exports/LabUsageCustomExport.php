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
                Column::make('location_id')
                    ->heading('Location')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->location_id
                            ? $record->location->name 
                            : '-'
                    ),
                Column::make('grade_id')
                    ->heading('Grade')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->grade_id
                            ? $record->grade->name
                            : '-'
                    ),
                Column::make('num_students')->heading('Students'),
                Column::make('lab_function')
                    ->heading('Lab Function')
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->lab_function 
                            ? $record->lab_function
                            : '-'
                    ),
                Column::make('end_state')->heading('End State'),
                Column::make('notes')->heading('Notes'),
                Column::make('created_at')->heading('Created At'),
                Column::make('updated_at')->heading('Updated At'),
            ]);
    }
}
