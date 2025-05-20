<?php

namespace App\Filament\Resources\AboutResource\Pages;

use App\Filament\Resources\AboutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAbout extends EditRecord
{
    protected static string $resource = AboutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back')
                ->icon('heroicon-o-arrow-left') // Using Heroicons arrow-left icon
                ->url($this->getResource()::getUrl('index')), // Links back to the index page

            // Uncomment if you want to keep the delete action
            // Actions\DeleteAction::make(),
        ];
    }
}
