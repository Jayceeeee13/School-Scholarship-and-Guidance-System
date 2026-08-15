<?php

namespace App\Exports;

use App\Models\Students;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class StudentsExport implements FromCollection, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $studentIds;

    public function __construct($studentIds = null)
    {
        $this->studentIds = $studentIds;
    }

    public function title(): string
    {
        return 'Students';
    }

    public function collection()
    {
        $empty = collect([
            collect(array_fill(0, 22, '')),
            collect(array_fill(0, 22, '')),
            collect(array_fill(0, 22, '')),
        ]);

        $query = Students::query()->with(['gender', 'program'])->orderBy('created_at', 'desc');
        if ($this->studentIds) {
            $query->whereIn('id', $this->studentIds);
        }

        return $empty->concat($query->get());
    }

    public function map($row): array
    {
        if (is_a($row, \Illuminate\Support\Collection::class)) {
            return array_fill(0, 22, '');
        }

        static $seq = 0;
        $seq++;

        return [
            $seq,
            $row->student_id ?? '',
            $row->last_name ?? '',
            $row->first_name ?? '',
            $row->extension_name ?? '',
            $row->middle_name ?? '',
            $row->gender?->name ?? '',
            $row->birth_date ? \Carbon\Carbon::parse($row->birth_date)->format('Y-m-d') : '',
            $row->program?->name ?? '',
            $row->year_level ?? '',
            $row->fathers_lastname ?? '',
            $row->fathers_firstname ?? '',
            $row->fathers_middlename ?? '',
            $row->mothers_lastname ?? '',
            $row->mothers_firstname ?? '',
            $row->mothers_middlename ?? '',
            $row->address ?? '',
            $row->zipcode ?? '',
            $row->disability ?? '',
            $row->contact_no ?? '',
            $row->email ?? '',
            $row->ip_group ?? '',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ── Row heights ────────────────────────────────────────
                $sheet->getRowDimension(1)->setRowHeight(20);
                $sheet->getRowDimension(2)->setRowHeight(30);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // ── Row 1: group labels ────────────────────────────────
                $sheet->setCellValue('A1', 'SEQ');
                $sheet->setCellValue('B1', 'STUDENT ID');
                $sheet->setCellValue('C1', "STUDENT'S NAME");
                $sheet->setCellValue('G1', "STUDENT'S PROFILE");
                $sheet->setCellValue('K1', "FATHER'S NAME");
                $sheet->setCellValue('N1', "MOTHER'S MAIDEN NAME"); // merged N1:P1
                $sheet->setCellValue('Q1', 'PERMANENT ADDRESS');

                // ── Merges ─────────────────────────────────────────────
                $sheet->mergeCells('C1:F1');
                $sheet->mergeCells('G1:J1');
                $sheet->mergeCells('K1:M1');
                $sheet->mergeCells('N1:P1');
                $sheet->mergeCells('Q1:V1');

                // ── Row 2: sub-headers (written AFTER merges) ──────────
                // Writing after merges ensures PhpSpreadsheet doesn't
                // reset these values when the merge is applied.
                $sheet->setCellValue('C2', 'LAST NAME');
                $sheet->setCellValue('D2', 'GIVEN NAME');
                $sheet->setCellValue('E2', 'EXT. NAME');
                $sheet->setCellValue('F2', 'MIDDLE NAME');
                $sheet->setCellValue('G2', 'SEX');
                $sheet->setCellValue('H2', 'BIRTHDATE');
                $sheet->setCellValue('I2', 'COMPLETE PROGRAM NAME');
                $sheet->setCellValue('J2', 'YEAR LEVEL');
                $sheet->setCellValue('K2', 'LAST NAME');
                $sheet->setCellValue('L2', 'GIVEN NAME');
                $sheet->setCellValue('M2', 'MIDDLE NAME');
                $sheet->setCellValue('N2', 'LAST NAME');
                $sheet->setCellValue('O2', 'GIVEN NAME');
                $sheet->setCellValue('P2', 'MIDDLE NAME');
                $sheet->setCellValue('Q2', 'STREET & BARANGAY');
                $sheet->setCellValue('R2', 'ZIPCODE');
                $sheet->setCellValue('S2', 'DISABILITY (if Applicable)');
                $sheet->setCellValue('T2', 'CONTACT NUMBER');
                $sheet->setCellValue('U2', 'EMAIL ADDRESS');
                $sheet->setCellValue('V2', 'INDIGENOUS PEOPLE GROUP');

                // ── Row 3: hints ───────────────────────────────────────
                $sheet->setCellValue('H3', '(yyyy-mm-dd)');
                $sheet->setCellValue('J3', '(1,2,3,4,5,6)');
                $sheet->setCellValue('R3', '(TES Applicant)');
                $sheet->setCellValue('V3', '(indicate the specific IP)');

                // ── Row 1 style: no wrapText, clipped to row height ────
                $sheet->getStyle('A1:V1')->applyFromArray([
                    'font'      => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true, 'size' => 10],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => false,
                        'shrinkToFit'=> true,   // shrink font to fit in row 1 height
                    ],
                    'borders'   => [
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
                    ],
                ]);

                // ── Row 2 style ────────────────────────────────────────
                $sheet->getStyle('A2:V2')->applyFromArray([
                    'font'      => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true, 'size' => 10],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E88E5']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);

                // ── Row 3 style ────────────────────────────────────────
                $sheet->getStyle('A3:V3')->applyFromArray([
                    'font'      => ['color' => ['rgb' => '0D47A1'], 'bold' => false, 'size' => 9, 'italic' => true],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BBDEFB']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                ]);

                // ── Data rows ──────────────────────────────────────────
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 4) {
                    $sheet->getStyle("A4:V{$lastRow}")->applyFromArray([
                        'font'      => ['size' => 10],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                        'borders'   => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color'       => ['rgb' => 'BDBDBD'],
                            ],
                        ],
                    ]);

                    for ($r = 4; $r <= $lastRow; $r++) {
                        if ($r % 2 === 0) {
                            $sheet->getStyle("A{$r}:V{$r}")->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F5F5F5');
                        }
                    }
                }

                $sheet->freezePane('A4');
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
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