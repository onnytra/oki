<?php

namespace App\Filament\User\Resources\TryoutResource\Pages;

use App\Filament\User\Resources\TryoutResource;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\Card;
use Filament\Resources\Pages\ViewRecord;

class ViewTryout extends ViewRecord
{
    protected static string $resource = TryoutResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        Card::make()
                            ->schema([
                                TextEntry::make('exam.name')
                                    ->label('Exam Name')
                                    ->weight('bold')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->columnSpanFull(),
                                
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('exam.start')
                                            ->label('Start Date & Time')
                                            ->dateTime('d M Y, H:i')
                                            ->icon('heroicon-o-calendar'),
                                            
                                        TextEntry::make('exam.end')
                                            ->label('End Date & Time')
                                            ->dateTime('d M Y, H:i')
                                            ->icon('heroicon-o-clock'),
                                            
                                        TextEntry::make('duration')
                                            ->state(function ($record) {
                                                $start = \Carbon\Carbon::parse($record->exam->start);
                                                $end = \Carbon\Carbon::parse($record->exam->end);
                                                return $start->diffForHumans($end, ['parts' => 2]);
                                            })
                                            ->label('Duration')
                                            ->icon('heroicon-o-clock'),
                                            
                                        TextEntry::make('status')
                                            ->state(function ($record) {
                                                $now = now();
                                                $start = \Carbon\Carbon::parse($record->exam->start);
                                                $end = \Carbon\Carbon::parse($record->exam->end);
                                                
                                                if ($now < $start) return 'Upcoming';
                                                if ($now >= $start && $now <= $end) return 'Ongoing';
                                                return 'Ended';
                                            })
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'Upcoming' => 'info',
                                                'Ongoing' => 'success',
                                                'Ended' => 'danger',
                                            })
                                            ->icon('heroicon-o-signal'),
                                    ]),
                            ]),
                            
                        Card::make()
                            ->schema([
                                TextEntry::make('exam.description')
                                    ->label('Description')
                                    ->html()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                            
                        Card::make()
                            ->schema([
                                TextEntry::make('exam.rules')
                                    ->label('Rules & Regulations')
                                    ->html()
                                    ->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}