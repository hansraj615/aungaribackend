<?php

namespace App\Filament\Resources\DarshanResource\Pages;

use App\Filament\Resources\DarshanResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDarshans extends ListRecords
{
    protected static string $resource = DarshanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
