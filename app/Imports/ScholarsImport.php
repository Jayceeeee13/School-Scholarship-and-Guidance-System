<?php

namespace App\Imports;

use App\Models\Scholars;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Carbon\Carbon;

class ScholarsImport implements ToModel, WithStartRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected int $termId;
    protected int $skippedDuplicates = 0;

    public function __construct(int $termId)
    {
        $this->termId = $termId;
    }

    public function getSkippedDuplicates(): int
    {
        return $this->skippedDuplicates;
    }

    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        if (empty(array_filter($row))) {
            return null;
        }

        // Skip duplicate: same first+last+middle name already exists in this term
        $exists = Scholars::where('term_id', $this->termId)
            ->where('first_name', $row[3] ?? null)
            ->where('last_name',  $row[2] ?? null)
            ->where('middle_name', $row[5] ?? null)
            ->exists();

        if ($exists) {
            $this->skippedDuplicates++;
            return null;
        }

        // Handle birthdate
        $birthdate = $row[7] ?? null;
        if (is_numeric($birthdate)) {
            try {
                $birthdate = Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($birthdate))->format('Y-m-d');
            } catch (\Exception $e) {
                $birthdate = null;
            }
        } elseif ($birthdate) {
            try {
                $birthdate = Carbon::parse($birthdate)->format('Y-m-d');
            } catch (\Exception $e) {
                $birthdate = null;
            }
        }

        // Handle year level
        $yearLevel = $row[9] ?? null;
        if ($yearLevel) {
            preg_match('/(\d)/', (string) $yearLevel, $matches);
            $yearLevel = $matches[1] ?? $yearLevel;
        }

        // Default type_of_scholarship
        $typeOfScholarship = trim((string) ($row[10] ?? ''));
        $typeOfScholarship = ($typeOfScholarship !== '') ? $typeOfScholarship : 'No Scholarship';

        return new Scholars([
            'term_id'             => $this->termId,
            'student_id'          => $this->nullIfBlank($row[1]  ?? null),
            'last_name'           => $row[2]  ?? null,
            'first_name'          => $row[3]  ?? null,
            'extension_name'      => $this->nullIfBlank($row[4]  ?? null),
            'middle_name'         => $row[5]  ?? null,
            'sex'                 => trim($row[6] ?? ''),
            'birthdate'           => $birthdate,
            'program'             => $row[8]  ?? null,
            'year_level'          => $yearLevel,
            'type_of_scholarship' => $typeOfScholarship,
            'batch_no'            => $this->nullIfBlank($row[11] ?? null),
            'ip_group'            => $this->nullIfBlank($row[12] ?? null),
            'pwd'                 => $this->nullIfBlank($row[13] ?? null),
            'benefit'             => $this->nullIfBlank($row[14] ?? null),
            'status'              => $row[15] ?? 'active',
        ]);
    }

    private function nullIfBlank($value): mixed
    {
        if ($value === null) return null;
        return trim((string) $value) !== '' ? $value : null;
    }

    public function rules(): array
    {
        return [
            '2'  => 'required|string|max:200',
            '3'  => 'required|string|max:200',
            '4'  => 'nullable|string|max:200',
            '5'  => 'required|string|max:200',
            '6'  => 'required|in:Male,Female,MALE,FEMALE,male,female,M,F',
            '7'  => 'required',
            '8'  => 'required|string|max:200',
            '9'  => 'nullable',
            '10' => 'nullable|string|max:255',
            '1'  => 'nullable|numeric',
            '11' => 'nullable|numeric',
            '12' => 'nullable|string|max:255',
            '13' => 'nullable|string|max:255',
            '14' => 'nullable|numeric',
            '15' => 'nullable|in:active,inactive,graduated,discontinued,Active,Inactive,Graduated,Discontinued',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '3.required'  => 'Given name (first name) is required',
            '5.required'  => 'Middle name is required',
            '2.required'  => 'Last name is required',
            '6.required'  => 'Sex is required',
            '6.in'        => 'Sex must be Male or Female',
            '7.required'  => 'Birthdate is required',
            '8.required'  => 'Program is required',
        ];
    }
}