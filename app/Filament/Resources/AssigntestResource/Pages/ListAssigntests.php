<?php

namespace App\Filament\Resources\AssigntestResource\Pages;

use App\Filament\Resources\AssigntestResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAssigntests extends ListRecords
{
    protected static string $resource = AssigntestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
