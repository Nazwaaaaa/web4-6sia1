<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UsersExport implements FromQuery, WithMapping, ShouldAutoSize, WithHeadings, WithStyles
{
    protected $no = 1;

    public function query()
    {
        return User::query();
    }

    public function headings():array
    {
        return [
            'NO',
            'NAMA LENGKAP',
            'EMAIL',
            'USERNAME',
            'GENDER',
            'STATUS',
            'ALAMAT DOMISILI',
        ];
    }

    public function map($row):array
    {
        return[
            $this->no++,
            $row->name,
            $row->email,
            $row->username,
            $row->gender ? 'Pria' : 'Wanita',
            $row->is_active ? 'Aktif' : 'Nonaktif',
            $row->address,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return[
            1 => ['font' => [
                'bold' => true,
                'size' => 14
                ],
            ],
        ];
    }
}
