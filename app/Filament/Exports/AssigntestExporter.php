<?php

namespace App\Filament\Exports;

use App\Models\Assigntest;
use App\Models\Exam;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Forms\Components\Select;

class AssigntestExporter extends Exporter
{
    protected static ?string $model = Assigntest::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('user.name'),
            ExportColumn::make('exam.name'),
            ExportColumn::make('total_questions')
                ->label('Total Soal')
                ->state(function (Assigntest $record): int {
                    return $record->answers()->count();
                }),
            ExportColumn::make('total_score')
                ->label('Total Nilai')
                ->state(function (Assigntest $record): int {
                    return $record->answers()->sum('score');
                }),
        ];
    }

    public static function getFormSchema(): array
    {
        return [
            Select::make('exam_id')
                ->label('Pilih Ujian')
                ->options(Exam::query()->pluck('name', 'id'))
                ->required()
                ->searchable()
        ];
    }

    public function getFilteredQuery($query)
    {
        return $query->when(
            $this->form->data['exam_id'] ?? null,
            fn ($query, $examId) => $query->where('exam_id', $examId)
        );
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your assigntest export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
