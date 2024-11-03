<?php

namespace App\Filament\Resources\ExamResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\MaxWidth;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\HtmlString;
use Mohamedsabil83\FilamentFormsTinyeditor\Components\TinyEditor;

class QuestionsRelationManager extends RelationManager
{
    protected static string $relationship = 'questions';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TinyEditor::make('question')
                    ->required()
                    ->columnSpanFull()
                    ->fileAttachmentsDirectory('question_files')
                    ->fileAttachmentsVisibility('public'),
                Forms\Components\Repeater::make('options')
                    ->label('Options')
                    ->relationship('questionoptions')
                    ->columnSpanFull()
                    ->schema([
                        TinyEditor::make('option')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('question_files')
                            ->fileAttachmentsVisibility('public'),
                        Forms\Components\Toggle::make('is_true')
                            ->required()
                            ->label('Is True'),
                    ]),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('exam_id')
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->badge()
                    ->formatStateUsing(fn() => 'Show Question')
                    ->label('Question')
                    ->action(
                        Tables\Actions\Action::make('viewQuestion')
                            ->label('View Question')
                            ->modalContent(fn($record) => new HtmlString($record->question))
                            ->modalHeading('Question Detail')
                            ->modalWidth(MaxWidth::ThreeExtraLarge)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->closeModalByClickingAway()
                    ),
                Tables\Columns\TextColumn::make('questionoptions_count')
                    ->badge()
                    ->color('success')
                    ->label('Total Option')
                    ->counts('questionoptions')
                    ->action(
                        Tables\Actions\Action::make('viewOption')
                            ->label('View Option')
                            ->modalContent(function ($record) {
                                $optionsHtml = '<div class="space-y-4">';

                                foreach ($record->questionoptions as $index => $option) {
                                    $optionsHtml .= '
                                        <div class="pb-4 border-b border-gray-200">
                                            <div class="font-medium mb-2">Option ' . ($index + 1) . '</div>
                                            ' . $option->option . '
                                        </div>';
                                }

                                $optionsHtml .= '</div>';

                                return new HtmlString($optionsHtml);
                            })
                            ->modalHeading('Option Detail')
                            ->modalWidth(MaxWidth::ThreeExtraLarge)
                            ->modalSubmitAction(false)
                            ->modalCancelAction(false)
                            ->closeModalByClickingAway()
                    ),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
