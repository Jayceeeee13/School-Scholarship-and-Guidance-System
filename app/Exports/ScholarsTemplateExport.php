<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class ScholarsTemplateExport implements FromArray, WithStyles, WithColumnWidths, WithEvents
{
    public function array(): array
    {
        return [
            // Row 1 - Group headers
            ['SEQ', 'STUDENT ID', "STUDENT'S NAME", null, null, null, "STUDENT'S PROFILE", null, null, null, null, null, null, null, null, null],
            // Row 2 - Column headers
            [null, null, 'LAST NAME', 'GIVEN NAME', 'EXT. NAME', 'MIDDLE NAME', 'SEX', 'BIRTHDATE', 'COMPLETE PROGRAM NAME', 'YEAR LEVEL', 'TYPE OF SCHOLARSHIP', 'BATCH NO.', 'IP GROUP', 'PWD', 'BENEFIT', 'STATUS'],
            // Row 3 - Hints
            [null, null, null, null, null, null, null, '(yyyy-mm-dd)', null, '(1,2,3,4,5)', null, null, null, 'Deaf', null, '(active/inactive/graduated/discontinued)'],
            // Row 4+ - Sample data
            [1, '', 'Dela Cruz', 'Juan', 'Jr.', 'Reyes',  'Male',   '2000-01-15', 'BSIT', '1', 'Academic Scholarship', '2026', 'Cebuano', 'Deaf',  '15000', 'active'],
            [2, '', 'Garcia',   'Maria', '',    'Santos', 'Female', '2001-05-20', 'BSCS', '2', 'Athletic Scholarship', '2026', '',        'ADHD
            ', '20000', 'active'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Row 1 - Group header
            1 => [
                'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Row 2 - Column header
            2 => [
                'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
            // Row 3 - Hints
            3 => [
                'font'      => ['italic' => true, 'size' => 9, 'color' => ['rgb' => '757575']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F5F5F5']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
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
            'P' => 15, // STATUS (wider for hint text)
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Merge header rows ────────────────────────────────────────
                $sheet->mergeCells('A1:A3'); // SEQ
                $sheet->mergeCells('B1:B3'); // STUDENT ID
                $sheet->mergeCells('C1:F1'); // STUDENT'S NAME group
                $sheet->mergeCells('G1:P1'); // STUDENT'S PROFILE group
                $sheet->mergeCells('C2:C3'); // LAST NAME
                $sheet->mergeCells('D2:D3'); // GIVEN NAME
                $sheet->mergeCells('E2:E3'); // EXT. NAME
                $sheet->mergeCells('F2:F3'); // MIDDLE NAME
                $sheet->mergeCells('G2:G3'); // SEX
                // H2/H3 not merged - hint on row 3
                $sheet->mergeCells('I2:I3'); // COMPLETE PROGRAM NAME
                // J2/J3 not merged - hint on row 3
                $sheet->mergeCells('K2:K3'); // TYPE OF SCHOLARSHIP
                $sheet->mergeCells('L2:L3'); // BATCH NO.
                $sheet->mergeCells('M2:M3'); // IP GROUP
                // N2/N3 not merged - hint on row 3
                $sheet->mergeCells('O2:O3'); // BENEFIT
                // P2/P3 not merged - hint on row 3

                // ── Row heights ──────────────────────────────────────────────
                $sheet->getRowDimension(1)->setRowHeight(25);
                $sheet->getRowDimension(2)->setRowHeight(25);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // ── Border around all header rows ────────────────────────────
                $sheet->getStyle('A1:P3')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'BDBDBD'],
                        ],
                    ],
                ]);

                // ── Style sample data rows ───────────────────────────────────
                $sheet->getStyle('A4:P5')->applyFromArray([
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFF9C4'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => 'BDBDBD'],
                        ],
                    ],
                ]);

                // ── Freeze header rows ───────────────────────────────────────
                $sheet->freezePane('A4');

                // ── Add a note above sample rows ─────────────────────────────
                $sheet->getComment('A4')->getText()->createTextRun('Sample data — delete before importing.');
            },
        ];
    }
}