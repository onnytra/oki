<?php

namespace App\Filament\Exports;

use App\Models\User;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Redirect;

class UserExporter extends Exporter
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            ExportColumn::make('nis'),
            ExportColumn::make('name'),
            ExportColumn::make('email'),
            ExportColumn::make('phone_number'),
            ExportColumn::make('school'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your user export has completed, and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';
        $failedRowsCount = $export->getFailedRowsCount();
        $latestExport = $export;

        $fileUrl = url("storage/filament_exports/{$latestExport->id}/{$latestExport->file_name}.xlsx");

        if ($failedRowsCount > 0) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
            Notification::make()
                ->title('Export Completed with Some Failures')
                ->body(
                    "<a href=\"{$fileUrl}\" target=\"_blank\">CLICK HERE</a>")
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title('Export Completed Successfully')
                ->body(
                    "<a href=\"{$fileUrl}\" target=\"_blank\">CLICK HERE</a>"
                )
                ->success()
                ->send();
        }

        return $body;
    }
}
