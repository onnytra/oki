<?php

namespace App\Filament\Resources\AssigntestResource\Pages;

use App\Filament\Resources\AssigntestResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Grid;

class ViewUserTest extends ViewRecord
{
    protected static string $resource = AssigntestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Exam Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Participant'),
                                TextEntry::make('exam.name')
                                    ->label('Exam Name'),
                                TextEntry::make('exam.start')
                                    ->label('Date of Exam')
                                    ->dateTime('d M Y, H:i'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('answers_count')
                                    ->label('Total Question')
                                    ->state(fn($record) => $record->answers()->count()),
                                TextEntry::make('total_score')
                                    ->label('Total Score')
                                    ->badge()
                                    ->color(fn($state) => $state > 0 ? 'success' : 'danger')
                                    ->state(fn($record) => $record->answers()->sum('score')),
                            ]),
                    ]),
                Section::make('Answer Analysis')
                    ->description('Summary of answers results')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('correct_answers')
                                    ->label('Correct Answers')
                                    ->state(fn($record) => $record->answers()->where('score', '>', 0)->count())
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('wrong_answers')
                                    ->label('Wrong Answers')
                                    ->state(fn($record) => $record->answers()->where('score', 0)->count())
                                    ->badge()
                                    ->color('danger'),
                                TextEntry::make('score_percentage')
                                    ->label('Percentage of Score')
                                    ->state(function ($record) {
                                        $totalQuestions = $record->answers()->count();
                                        $correctAnswers = $record->answers()->where('score', '>', 0)->count();
                                        return $totalQuestions > 0
                                            ? round(($correctAnswers / $totalQuestions) * 100, 2) . '%'
                                            : '0%';
                                    })
                                    ->badge()
                                    ->color('info'),
                            ]),
                    ]),
                Section::make('Detail Answer')
                    ->description('Answer Details For Each Question')
                    ->collapsible()
                    ->schema([
                        RepeatableEntry::make('answers')
                            ->schema([
                                Grid::make(4)
                                    ->schema([
                                        TextEntry::make('question.question')
                                            ->label('Question')
                                            ->html()
                                            ->columnSpan(2),
                                        TextEntry::make('score')
                                            ->label('Score')
                                            ->badge()
                                            ->color(fn($state) => $state > 0 ? 'success' : 'danger'),
                                    ]),
                                Grid::make(2)
                                    ->schema([
                                        TextEntry::make('questionoption.option')
                                            ->html()
                                            ->label('Jawaban Dipilih'),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['answers'] = $this->record->answers()
            ->with(['question', 'questionoption'])  // sesuaikan dengan nama relasi
            ->orderBy('id')
            ->get();

        return $data;
    }
}