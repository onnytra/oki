<?php

namespace App\Filament\Imports;

use App\Models\Admin;
use App\Models\User;
use Faker\Provider\ar_EG\Text;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Filament\Notifications\Notification;
use Illuminate\Support\Str;


class UserImporter extends Importer
{
    protected static ?string $model = User::class;
    public static function getColumns(): array
    {
        return [
            ImportColumn::make('nis')
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
            ImportColumn::make('password')
                ->fillRecordUsing(function (User $record) {
                    $record->password = Str::random(20);
                })
            // ImportColumn::make('password')
            //     ->requiredMapping()
            //     ->rules(['required', 'max:255']),
        ];
    }

    public function resolveRecord(): ?User
    {
        return new User();
    }


    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}