<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class UsersExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return User::select('name', 'email', 'created_at', 'updated_at')->get();
    }

    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Created At',
            'Updated At',
        ];
    }
}
