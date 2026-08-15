<?php

namespace App\Http\Controllers;

use App\Models\Scholars;
use App\Models\Term;
use Illuminate\Http\Request;

class ReportsPrintController extends Controller
{
    public function __invoke(Request $request)
    {
        $schoolYear = $request->get('school_year');

        if (! $schoolYear) {
            $schoolYear = Term::where('is_active', true)->value('school_year');
        }

        $term1 = null;
        $term2 = null;

        if ($schoolYear) {
            $allTerms = Term::where('school_year', $schoolYear)->get();

            $term1 = $allTerms->first(function ($t) {
                $s = strtolower($t->semester);
                return str_contains($s, '1st') || str_contains($s, 'first');
            });

            $term2 = $allTerms->first(function ($t) {
                $s = strtolower($t->semester);
                return str_contains($s, '2nd') || str_contains($s, 'second');
            });
        }

        $categories = ['TES', 'TDP', 'CMSP'];

        $rows = [];
        foreach ($categories as $cat) {
            $rows[$cat] = [
                'term1' => $this->termStats($term1, $cat),
                'term2' => $this->termStats($term2, $cat),
            ];
        }

        // View path matches: resources/views/filament/pages/reports-print.blade.php
        return view('filament.pages.reports-print', [
            'schoolYear' => $schoolYear,
            'term1'      => $term1,
            'term2'      => $term2,
            'categories' => $categories,
            'rows'       => $rows,
            'subGroups'  => ['No. OF GRANTEES', 'PWD', 'IP', 'NONE BOARD', 'WITH BOARD'],
        ]);
    }

    private function termStats(?Term $term, ?string $cat = null): array
    {
        $empty = [
            'total_male' => 0, 'total_female' => 0, 'total' => 0,
            'pwd_male'   => 0, 'pwd_female'   => 0,
            'ip_male'    => 0, 'ip_female'    => 0,
            'none_board_male'  => 0, 'none_board_female'  => 0,
            'with_board_male'  => 0, 'with_board_female'  => 0,
        ];

        if (! $term) return $empty;

        $query = Scholars::where('term_id', $term->id)->where('status', 'active');

        if ($cat) {
            $keyword = strtolower($cat);
            $query->whereRaw('LOWER(type_of_scholarship) LIKE ?', ["%{$keyword}%"]);
        }

        $scholars = $query->get();
        $male     = $scholars->where('sex', 'Male');
        $female   = $scholars->where('sex', 'Female');

        return [
            'total_male'        => $male->count(),
            'total_female'      => $female->count(),
            'total'             => $scholars->count(),
            'pwd_male'          => $male->filter(fn ($s) => strtolower($s->pwd ?? '') === 'yes')->count(),
            'pwd_female'        => $female->filter(fn ($s) => strtolower($s->pwd ?? '') === 'yes')->count(),
            'ip_male'           => $male->filter(fn ($s) => ! empty($s->ip_group))->count(),
            'ip_female'         => $female->filter(fn ($s) => ! empty($s->ip_group))->count(),
            'none_board_male'   => $male->filter(fn ($s) => ! str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'))->count(),
            'none_board_female' => $female->filter(fn ($s) => ! str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'))->count(),
            'with_board_male'   => $male->filter(fn ($s) => str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'))->count(),
            'with_board_female' => $female->filter(fn ($s) => str_contains(strtolower($s->type_of_scholarship ?? ''), 'board'))->count(),
        ];
    }
}