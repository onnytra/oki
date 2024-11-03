<?php

namespace App\Policies;

use App\Models\Admin;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImportPolicy
{
    use HandlesAuthorization;

    public function view(?Admin $admin, Import $import): bool
    {
        // Pastikan admin yang membuat import yang bisa mengakses
        return $admin && $import->admin()->is($admin);
    }
}