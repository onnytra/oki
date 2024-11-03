<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import as ModelsImport;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;
    
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nis')
                ->label('NIS')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'unique:users,nis']),
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255', 'unique:users,name']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('phone_number')
                ->requiredMapping()
                ->rules(['required', 'max:20']),
            ImportColumn::make('school')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?User
    {
        $user = new User();

        $user->password = Str::random(20);
        return $user;
    }

    public static function getCompletedNotificationBody(ModelsImport $import): string
    {
        $body = 'Total ' . number_format($import->successful_rows) . ' ' . str('baris')->plural($import->successful_rows) . ' berhasil diimport.';
        $failedRowsCount = $import->getFailedRowsCount();
        if ($failedRowsCount > 0) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('baris')->plural($failedRowsCount) . ' gagal diimport.';
            Notification::make()
                ->title('Import Completed with Some Failures')
                ->body($body)
                ->warning()
                ->send();
        } else {
            Notification::make()
                ->title('Import Completed Successfully')
                ->body($body)
                ->success()
                ->send();
        }

        return $body;
    }
}
