<?php

namespace App\Filament\User\Resources\TryoutResource\Pages;

use App\Filament\User\Resources\TryoutResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTryout extends EditRecord
{
    protected static string $resource = TryoutResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
