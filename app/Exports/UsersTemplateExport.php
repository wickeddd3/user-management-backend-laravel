<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class UsersTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['name', 'email', 'password'],
        ];
    }
}
