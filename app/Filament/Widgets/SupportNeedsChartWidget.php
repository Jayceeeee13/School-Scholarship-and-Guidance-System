<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CounselingAppointments;
use Illuminate\Support\Facades\DB;

class SupportNeedsChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Support Needs Distribution';

    protected static string $color = 'info';

    // Takes right column on md/lg, full width on mobile
    protected int | string | array $columnSpan = [
    'default' => 'full',
    'md'      => 3,
];
protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isGuidance();
    }

    protected function getData(): array
    {
        $supportNeeds = CounselingAppointments::query()
            ->with('supportNeeded')
            ->whereNotNull('support_needed_id')
            ->select('support_needed_id', DB::raw('count(*) as count'))
            ->groupBy('support_needed_id')
            ->orderByDesc('count')
            ->get();

        if ($supportNeeds->isEmpty()) {
            return [
                'datasets' => [[
                    'label' => 'Appointments',
                    'data'  => [0],
                    'backgroundColor' => ['rgb(209, 213, 219)'],
                ]],
                'labels' => ['No data available'],
            ];
        }

        $labels = [];
        $data   = [];
        $total  = $supportNeeds->sum('count');

        $colors = [
            'rgb(59, 130, 246)',
            'rgb(16, 185, 129)',
            'rgb(251, 191, 36)',
            'rgb(239, 68, 68)',
            'rgb(139, 92, 246)',
            'rgb(236, 72, 153)',
            'rgb(249, 115, 22)',
            'rgb(14, 165, 233)',
        ];

        foreach ($supportNeeds as $need) {
            $percentage = round(($need->count / $total) * 100, 1);
            $labels[]   = ($need->supportNeeded->name ?? 'Unknown') . ' (' . $percentage . '%)';
            $data[]     = $need->count;
        }

        return [
            'datasets' => [[
                'label'           => 'Appointments',
                'data'            => $data,
                'backgroundColor' => array_slice($colors, 0, count($data)),
                'borderColor'     => array_slice($colors, 0, count($data)),
                'borderWidth'     => 1,
                'borderRadius'    => 6,
                'borderSkipped'   => false,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => false],
                'tooltip' => ['enabled' => true],
            ],
            'scales' => [
                'x' => [
                    'grid'  => ['display' => false],
                    'ticks' => ['font' => ['size' => 11]],
                ],
                'y' => [
                    'beginAtZero' => true,
                    'ticks'       => ['stepSize' => 1, 'font' => ['size' => 11]],
                ],
            ],
            'maintainAspectRatio' => true,
            'responsive'          => true,
        ];
    }

    public function getDescription(): ?string
    {
        $total  = CounselingAppointments::whereNotNull('support_needed_id')->count();
        $unique = CounselingAppointments::distinct('support_needed_id')
            ->whereNotNull('support_needed_id')
            ->count('support_needed_id');

        return "Total: {$total} appointments | {$unique} support types";
    }
}