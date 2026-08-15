<?php

namespace App\Imports;

use App\Models\Students;
use App\Models\Gender;
use App\Models\Program;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\Importable;
use Carbon\Carbon;

class StudentsImport implements ToModel, WithStartRow, WithValidation, SkipsOnError, SkipsOnFailure
{
    use Importable, SkipsErrors, SkipsFailures;

    protected int $termId;
    protected array $genderCache  = [];
    protected array $programCache = [];
    protected ?object $allPrograms = null;
    protected ?object $allGenders  = null;

    public function __construct(int $termId)
    {
        $this->termId = $termId;
    }

    public function startRow(): int
    {
        return 4;
    }

    public function model(array $row)
    {
        if (empty(array_filter($row, fn($v) => $v !== null && trim((string) $v) !== ''))) {
            return null;
        }

        // ── Birthdate ──────────────────────────────────────────────────
        $birthdate = $row[7] ?? null;
        if (is_numeric($birthdate) && $birthdate > 0) {
            try {
                $birthdate = Carbon::instance(
                    \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((int) $birthdate)
                )->format('Y-m-d');
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

        // ── Resolve gender_id ──────────────────────────────────────────
        $sexRaw = trim((string) ($row[6] ?? ''));
        $sexKey = strtolower($sexRaw);
        if ($sexKey && ! isset($this->genderCache[$sexKey])) {
            if (! $this->allGenders) {
                $this->allGenders = Gender::all(['id', 'name']);
            }
            $genderId = $this->allGenders
                ->first(fn($g) => strtolower($g->name) === $sexKey)?->id;
            if (! $genderId) {
                $genderId = $this->allGenders
                    ->first(fn($g) => str_starts_with(strtolower($g->name), $sexKey))?->id;
            }
            $this->genderCache[$sexKey] = $genderId ?? null;
        }
        $genderId = $this->genderCache[$sexKey] ?? null;

        // ── Resolve program_id ─────────────────────────────────────────
        $programRaw = trim((string) ($row[8] ?? ''));
        $programKey = strtolower($programRaw);
        if ($programKey && ! isset($this->programCache[$programKey])) {
            if (! $this->allPrograms) {
                $this->allPrograms = Program::active()->get(['id', 'name']);
            }
            $programId = $this->allPrograms
                ->first(fn($p) => strtolower($p->name) === $programKey)?->id;
            if (! $programId) {
                $programId = $this->allPrograms
                    ->first(fn($p) => str_contains($programKey, strtolower($p->name)))?->id;
            }
            if (! $programId) {
                $programId = $this->allPrograms
                    ->first(fn($p) => str_contains(strtolower($p->name), $programKey))?->id;
            }
            if (! $programId) {
                $stopWords  = ['of', 'in', 'and', 'the', 'a', 'an', 'bs', 'ab'];
                $excelWords = array_diff(array_filter(explode(' ', $programKey)), $stopWords);
                $bestScore  = 0;
                $bestId     = null;
                foreach ($this->allPrograms as $program) {
                    $dbWords = array_diff(array_filter(explode(' ', strtolower($program->name))), $stopWords);
                    $overlap = count(array_intersect($excelWords, $dbWords));
                    if ($overlap > $bestScore) {
                        $bestScore = $overlap;
                        $bestId    = $program->id;
                    }
                }
                if ($bestScore >= 3) {
                    $programId = $bestId;
                }
            }
            $this->programCache[$programKey] = $programId ?? null;
        }
        $programId = $this->programCache[$programKey] ?? null;

        // ── Year level ─────────────────────────────────────────────────
        $yearLevel = trim((string) ($row[9] ?? ''));
        if ($yearLevel) {
            preg_match('/(\d)/', $yearLevel, $m);
            $yearLevel = $m[1] ?? $yearLevel;
        }

        // ── Nullable string helper ─────────────────────────────────────
        $str = fn($v) => ($v !== null && trim((string) $v) !== '') ? trim((string) $v) : null;

        return new Students([
            'student_id'          => $str($row[1]),
            'last_name'           => $str($row[2]),
            'first_name'          => $str($row[3]),
            'extension_name'      => $str($row[4]),
            'middle_name'         => $str($row[5]),
            'gender_id'           => $genderId,
            'birth_date'          => $birthdate,
            'program_id'          => $programId,
            'year_level'          => $yearLevel ?: null,
            'term_id'             => $this->termId,   // ← assigned from import form
            'fathers_lastname'    => $str($row[10]),
            'fathers_firstname'   => $str($row[11]),
            'fathers_middlename'  => $str($row[12]),
            'mothers_lastname'    => $str($row[13]),
            'mothers_firstname'   => $str($row[14]),
            'mothers_middlename'  => $str($row[15]),
            'address'             => $str($row[16]),
            'zipcode'             => $str($row[17]),
            'disability'          => $str($row[18]),
            'contact_no'          => $str($row[19]),
            'email'               => $str($row[20]),
            'ip_group'            => $str($row[21]),
        ]);
    }

    public function rules(): array
    {
        return [
            '1'  => 'nullable|max:100',
            '2'  => 'required|string|max:200',
            '3'  => 'required|string|max:200',
            '4'  => 'nullable|string|max:100',
            '5'  => 'nullable|string|max:200',
            '6'  => 'required|string|max:50',
            '7'  => 'required',
            '8'  => 'required|string|max:200',
            '9'  => 'required',
            '10' => 'nullable|string|max:255',
            '11' => 'nullable|string|max:200',
            '12' => 'nullable|string|max:255',
            '13' => 'nullable|string|max:255',
            '14' => 'nullable|string|max:200',
            '15' => 'nullable|string|max:255',
            '16' => 'nullable|string|max:500',
            '17' => 'nullable|max:200',
            '18' => 'nullable|string|max:200',
            '19' => 'nullable|max:20',
            '20' => 'nullable|email|max:200',
            '21' => 'nullable|string|max:500',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            '2.required' => 'Last name is required',
            '3.required' => 'First (given) name is required',
            '6.required' => 'Sex is required',
            '7.required' => 'Birthdate is required',
            '8.required' => 'Program name is required',
            '9.required' => 'Year level is required',
            '20.email'   => 'Email address format is invalid',
        ];
    }
}