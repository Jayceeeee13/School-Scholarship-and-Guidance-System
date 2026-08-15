<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    public function title(): string
    {
        return 'Students';
    }

    public function array(): array
    {
        // Two sample rows matching the 22-column template
        return [
            [
                1,
                '2024-00001',
                'Dela Cruz',
                'Juan',
                'Jr.',
                'Reyes',
                'Male',
                '2000-01-15',
                'Bachelor of Science in Information Technology',
                '1',
                'Dela Cruz',
                'Pedro',
                'Santos',
                'Garcia',
                'Maria',
                'Lopez',
                'Blk 1 Lot 2 Sampaguita St., Barangay San Jose',
                '1234',
                '',
                '09171234567',
                'juan.delacruz@email.com',
                '',
            ],
            [
                2,
                '2024-00002',
                'Santos',
                'Maria',
                '',
                'Cruz',
                'Female',
                '2001-05-20',
                'Bachelor of Science in Computer Science',
                '2',
                'Santos',
                'Roberto',
                'Bautista',
                'Cruz',
                'Ana',
                'Villanueva',
                'Blk 3 Lot 4 Rosal St., Barangay Poblacion',
                '5678',
                '',
                '09281234567',
                'maria.santos@email.com',
                '',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            ['SEQ ', 'STUDENT ID', "STUDENT'S NAME", '', '', '', "STUDENT'S PROFILE", '', '', '', "FATHER'S NAME", '', '', "MOTHER'S MAIDEN NAME", '', '', 'PERMANENT ADDRESS', '', '', '', '', ''],
            ['', '', 'LAST NAME', 'GIVEN NAME', 'EXT. NAME', 'MIDDLE NAME', 'SEX', 'BIRTHDATE', 'COMPLETE PROGRAM NAME', 'YEAR LEVEL', 'LAST NAME', 'GIVEN NAME', 'MIDDLE NAME', 'LAST NAME', 'GIVEN NAME', 'MIDDLE NAME', 'STREET & BARANGAY', 'ZIPCODE', 'DISABILITY (if Applicable)', 'CONTACT NUMBER', 'EMAIL ADDRESS', 'INDIGENOUS PEOPLE GROUP'],
            ['', '', '', '', '', '', '', '(yyyy-mm-dd)', '', '(1,2,3,4,5,6)', '', '', '', '', '', '', '', '(TES Applicant)', '', '', '', '(indicate the specific IP)'],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->mergeCells('C1:F1');
        $sheet->mergeCells('G1:J1');
        $sheet->mergeCells('K1:M1');
        $sheet->mergeCells('N1:P1');
        $sheet->mergeCells('Q1:V1');

        $headerFill    = ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2E7D32']];
        $subHeaderFill = ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '43A047']];
        $hintFill      = ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'C8E6C9']];
        $whiteFont     = ['color' => ['rgb' => 'FFFFFF'], 'bold' => true, 'size' => 10];
        $darkFont      = ['color' => ['rgb' => '1B5E20'], 'bold' => false, 'size' => 9, 'italic' => true];
        $center        = ['horizontal' => 'center', 'vertical' => 'center', 'wrapText' => true];

        $sheet->getStyle('A1:V1')->applyFromArray(['font' => $whiteFont, 'fill' => $headerFill,    'alignment' => $center]);
        $sheet->getStyle('A2:V2')->applyFromArray(['font' => $whiteFont, 'fill' => $subHeaderFill, 'alignment' => $center]);
        $sheet->getStyle('A3:V3')->applyFromArray(['font' => $darkFont,  'fill' => $hintFill,      'alignment' => $center]);

        $sheet->getStyle('A4:V5')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '424242']],
            'alignment' => ['vertical' => 'center'],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => 'A5D6A7'],
                ],
            ],
        ]);

        $sheet->getStyle('A4:V4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('F9FBE7');
        $sheet->getStyle('A5:V5')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('FFFFFF');

        $sheet->freezePane('A4');
        $sheet->getRowDimension(1)->setRowHeight(22);
        $sheet->getRowDimension(2)->setRowHeight(32);
        $sheet->getRowDimension(3)->setRowHeight(18);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,  'B' => 14, 'C' => 18, 'D' => 18, 'E' => 10,
            'F' => 18, 'G' => 8,  'H' => 14, 'I' => 22, 'J' => 10,
            'K' => 18, 'L' => 18, 'M' => 18, 'N' => 18, 'O' => 18,
            'P' => 18, 'Q' => 24, 'R' => 14, 'S' => 20, 'T' => 16,
            'U' => 24, 'V' => 28,
        ];
    }
}