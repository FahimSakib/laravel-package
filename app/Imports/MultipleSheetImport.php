<?php

namespace App\Imports;


use App\Imports\RolesImport;
use App\Imports\UsersImport;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithConditionalSheets;

class MultipleSheetImport implements WithMultipleSheets
{
    use WithConditionalSheets;

    public function conditionalSheets(): array
    {
        return [
            'roles' => new RolesImport(),
            'users' => new UsersImport(),
        
        ];
    }
}
