<?php

namespace App\Filament\User\Resources\TryoutResource\Pages;

use App\Filament\User\Resources\TryoutResource;
use Filament\Actions;
use Filament\Forms\Components\Builder;
use Filament\Resources\Pages\ListRecords;

class ListTryouts extends ListRecords
{
    protected static string $resource = TryoutResource::class;
}
