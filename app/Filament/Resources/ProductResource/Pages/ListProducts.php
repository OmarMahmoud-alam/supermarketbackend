<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Imports\ProductImport;
use Filament\Actions;
use pxlrbt\FilamentExcel\Columns\Column;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\ProductResource;
use EightyNine\ExcelImport\ExcelImportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            /*ExcelImportAction::make()
                ->color("primary"),*/
             ExcelImportAction::make()
                ->slideOver()
                ->color("primary")
                ->use(ProductImport::class)
                ,
             ExportAction::make() 
                ->exports([
                    ExcelExport::make()
                        ->fromTable()
                        ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                        ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                        ->withColumns([
                            Column::make('updated_at'),
                        ])
                ]), 
            Actions\CreateAction::make(
                
            ),
        ];
    }
}
