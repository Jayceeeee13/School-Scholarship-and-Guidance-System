<?php

namespace App\Exports;

use App\Models\Scholars;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ScholarsExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $scholarIds;

    public function __construct($scholarIds = null)
    {
        $this->scholarIds = $scholarIds;
    }

    public function query()
    {
        $query = Scholars::query()->orderBy('created_at', 'desc');

        if ($this->scholarIds) {
            $query->whereIn('id', $this->scholarIds);
        }

        return $query;
    }

    public function map($scholar): array
    {
        static $seq = 0;
        $seq++;

        return [
            $seq,                              // Col 0  - SEQ
            $scholar->student_id ?? '',        // Col 1  - STUDENT ID
            $scholar->last_name,               // Col 2  - LAST NAME
            $scholar->first_name,              // Col 3  - GIVEN NAME
            $scholar->extension_name ?? '',    // Col 4  - EXT. NAME
            $scholar->middle_name,             // Col 5  - MIDDLE NAME
            $scholar->sex,                     // Col 6  - SEX
            $scholar->birthdate
                ? $scholar->birthdate->format('Y-m-d')
                : '',                          // Col 7  - BIRTHDATE
            $scholar->program,                 // Col 8  - COMPLETE PROGRAM NAME
            $scholar->year_level,              // Col 9  - YEAR LEVEL
            $scholar->type_of_scholarship,     // Col 10 - TYPE OF SCHOLARSHIP
            $scholar->batch_no ?? '',          // Col 11 - BATCH NO.
            $scholar->ip_group ?? '',          // Col 12 - IP GROUP
            $scholar->pwd ?? '',               // Col 13 - PWD
            $scholar->benefit ?? '',           // Col 14 - BENEFIT
            $scholar->status,                  // Col 15 - STATUS
        ];
    }

    public function headings(): array
    {
        return [
            'SEQ',
            'STUDENT ID',
            'LAST NAME',
            'GIVEN NAME',
            'EXT. NAME',
            'MIDDLE NAME',
            'SEX',
            'BIRTHDATE',
            'COMPLETE PROGRAM NAME',
            'YEAR LEVEL',
            'TYPE OF SCHOLARSHIP',
            'BATCH NO.',
            'IP GROUP',
            'PWD',
            'BENEFIT',
            'STATUS',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'size'  => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2196F3'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,  // SEQ
            'B' => 15, // STUDENT ID
            'C' => 20, // LAST NAME
            'D' => 20, // GIVEN NAME
            'E' => 12, // EXT. NAME
            'F' => 20, // MIDDLE NAME
            'G' => 10, // SEX
            'H' => 15, // BIRTHDATE
            'I' => 25, // COMPLETE PROGRAM NAME
            'J' => 12, // YEAR LEVEL
            'K' => 25, // TYPE OF SCHOLARSHIP
            'L' => 12, // BATCH NO.
            'M' => 20, // IP GROUP
            'N' => 10, // PWD
            'O' => 15, // BENEFIT
            'P' => 15, // STATUS
        ];
    }
}