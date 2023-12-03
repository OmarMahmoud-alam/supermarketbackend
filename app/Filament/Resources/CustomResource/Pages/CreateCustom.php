<?php

namespace App\Filament\Resources\CustomResource\Pages;

use App\Filament\Resources\CustomResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCustom extends CreateRecord
{
    protected static string $resource = CustomResource::class;
}
