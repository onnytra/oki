<?php

namespace App\Filament\Resources\AssigntestResource\Pages;

use App\Filament\Resources\AssigntestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAssigntest extends EditRecord
{
    protected static string $resource = AssigntestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
