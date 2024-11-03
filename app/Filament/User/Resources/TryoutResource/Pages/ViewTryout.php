<?php

namespace App\Filament\User\Resources\TryoutResource\Pages;

use App\Filament\User\Resources\TryoutResource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;

class ViewTryout extends ViewRecord
{
    protected static string $resource = TryoutResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('exam.name')->columnSpanFull()->label('Exam'),
                TextEntry::make('exam.start')->label('Start Date Time'),
                TextEntry::make('exam.end')->label('End Date Time'),
                TextEntry::make('exam.description')->html()->columnSpanFull()->label('Description'),
                TextEntry::make('exam.rules')->html()->columnSpanFull()->label('Rules'),
            ]);
    }
}