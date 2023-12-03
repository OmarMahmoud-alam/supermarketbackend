<?php

namespace App\Filament\Resources\CustomResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\CustomResource;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ListCustoms extends ListRecords
{
    protected static string $resource = CustomResource::class;

    protected function getHeaderActions(): array
    {
        return [
         //   Actions\CreateAction::make(),
         ExportAction::make() 
         ->exports([
             ExcelExport::make()
                 ->fromTable()
                 ->withFilename(fn ($resource) => $resource::getModelLabel() . '-' . date('Y-m-d'))
                 ->withWriterType(\Maatwebsite\Excel\Excel::CSV)
                
         ]), 
        ];
    }
}
