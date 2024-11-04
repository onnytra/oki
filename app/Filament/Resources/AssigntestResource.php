<?php

namespace App\Filament\Resources;

use App\Filament\Exports\AssigntestExporter;
use App\Filament\Resources\AssigntestResource\Pages;
use App\Models\Assigntest;
use App\Models\Exam;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;


class AssigntestResource extends Resource
{
    protected static ?string $model = Assigntest::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Assign Tests & Monitoring';

    protected static ?string $modelLabel = 'Assign Test';

    protected static ?string $navigationGroup = 'Exam Management';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('exam_id')
                    ->label('Exam')
                    ->options(Exam::query()->pluck('name', 'id'))
                    ->required()
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('user_id', null)),

                Forms\Components\Section::make('User')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Select User')
                            ->multiple()
                            ->options(function (callable $get) {
                                $examId = $get('exam_id');

                                if (!$examId) {
                                    return [];
                                }

                                return User::query()
                                    ->whereDoesntHave('assigntests', function ($query) use ($examId) {
                                        $query->where('exam_id', $examId);
                                    })
                                    ->pluck('name', 'id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->minItems(1)
                            ->helperText('Pilih peserta yang belum ditugaskan ke ujian ini'),

                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('select_all')
                                ->label('Select All Participants')
                                ->icon('heroicon-m-check')
                                ->action(function (Forms\Set $set, Forms\Get $get) {
                                    $examId = $get('exam_id');

                                    if (!$examId) {
                                        return;
                                    }

                                    $allUserIds = User::query()
                                        ->whereDoesntHave('assigntests', function ($query) use ($examId) {
                                            $query->where('exam_id', $examId);
                                        })
                                        ->pluck('id')
                                        ->toArray();

                                    $set('user_id', $allUserIds);
                                })
                                ->visible(fn(Forms\Get $get) => (bool) $get('exam_id')),

                            Forms\Components\Actions\Action::make('clear_selection')
                                ->label('Clear Selection')
                                ->icon('heroicon-m-x-mark')
                                ->color('danger')
                                ->action(fn(Forms\Set $set) => $set('user_id', []))
                                ->visible(fn(Forms\Get $get) => (bool) $get('exam_id')),
                        ])
                            ->columnSpanFull()
                    ])
                    ->columns(1)
            ]);
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exam.name')
                    ->label('Exam')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('is_done')
                    ->label('Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'warning')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Done' : 'Not Done'),

                TextColumn::make('is_cheat')
                    ->label('Cheat Status')
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'danger' : 'success')
                    ->formatStateUsing(fn(bool $state): string => $state ? 'Cheating' : 'Not Cheating'),
                TextColumn::make('cheat_reason')
                    ->label('Cheat Reason')
                    ->searchable(),
                TextColumn::make('answers.score')
                    ->label('Score')
                    ->getStateUsing(function ($record) {
                        return $record->is_done ? $record->answers->sum('score') : 0;
                    })
                    ->badge()
                    ->color(fn(bool $state): string => $state ? 'success' : 'warning'),
            ])
            ->filters([
                SelectFilter::make('exam')
                    ->relationship('exam', 'name'),

                SelectFilter::make('is_done')
                    ->label('Status')
                    ->options([
                        '1' => 'Done',
                        '0' => 'Not Done',
                    ]),

                SelectFilter::make('is_cheat')
                    ->label('Cheat Status')
                    ->options([
                        '1' => 'Cheating',
                        '0' => 'Not Cheating',
                    ]),
            ])
            ->actions([
                Tables\Actions\Action::make('Remove Cheating Record')
                    ->label('Remove Cheating Record')
                    ->color('success')
                    ->action(function (Model $record) {
                        $record->is_cheat = 0;
                        $record->cheat_reason = null;
                        $record->save();
                    })
                    ->visible(fn(Assigntest $record) => $record->is_cheat)
                    ->requiresConfirmation(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(AssigntestExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListAssigntests::route('/'),
            'create' => Pages\CreateAssigntest::route('/create'),
            'view' => Pages\ViewUserTest::route('/{record}'),
        ];
    }
}
