<?php

namespace App\Filament\User\Resources;

use App\Filament\User\Resources\TryoutResource\Pages;
use App\Filament\User\Resources\TryoutResource\RelationManagers;
use App\Models\Answer;
use App\Models\Assigntest;
use App\Models\Exam;
use App\Models\Tryout;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TryoutResource extends Resource
{
    protected static ?string $model = Assigntest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Exams';
    protected static ?string $label = 'Exams';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('exam.name')
                    ->label('Exam')
                    ->searchable(),
                Tables\Columns\TextColumn::make('exam.start')
                    ->label('Start Date Time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('exam.end')
                    ->label('End Date Time')
                    ->searchable(),
                Tables\Columns\TextColumn::make('is_done')
                    ->label('Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Done' : 'Not Done'),
                Tables\Columns\TextColumn::make('is_cheat')
                    ->label('Cheat Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Cheating' : 'Not Cheating'),
                Tables\Columns\TextColumn::make('cheat_reason')
                    ->label('Cheat Reason')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                    Tables\Columns\TextColumn::make('answers.score')
                    ->label('Score')
                    ->getStateUsing(function ($record) {
                        if ($record->exam->show_result && $record->is_done) {
                            return $record->answers->sum('score');
                        }
                        return 'Not Available';
                    })
                    ->badge()
                    ->color('warning')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('Start Exam')
                    ->color('success')
                    ->requiresConfirmation()
                    ->icon('heroicon-o-play')
                    ->url(fn(Assigntest $record) => route('tryout.start', $record->id))
                    ->visible(fn(Assigntest $record) => $record->is_done == 0 && $record->exam->start < Carbon::now()),
            ])
            ->bulkActions([
                //
            ])
            ->modifyQueryUsing(function (Builder $query) {
                $query->whereHas('exam', function ($subQuery) {
                    $subQuery->where('status_exam', 1);
                })->where('user_id', auth()->id());
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTryouts::route('/'),
            'view' => Pages\ViewTryout::route('/{record}'),
        ];
    }
}
