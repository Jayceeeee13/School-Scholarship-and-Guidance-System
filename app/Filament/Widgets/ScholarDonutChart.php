<?php

namespace App\Filament\Widgets;

use App\Models\Scholars;
use Filament\Widgets\Widget;

class ScholarDonutChart extends Widget
{
    protected int | string | array $columnSpan = [
    'default' => 'full',
    'md'      => 3,
];
protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.scholar-donut-chart';

    public function getViewData(): array
    {
        $total    = Scholars::count();
        $active   = Scholars::where('status', 'active')->count();
        $inactive = Scholars::where('status', 'inactive')->count();
        $types    = Scholars::selectRaw('type_of_scholarship, count(*) as count')
            ->groupBy('type_of_scholarship')
            ->orderByDesc('count')
            ->get();

        $colors = [
            '#378ADD', '#1D9E75', '#7F77DD',
            '#EF9F27', '#D85A30', '#D4537E',
            '#639922', '#888780', '#E24B4A', '#5DCAA5',
        ];

        $typesWithColor = $types->map(function ($item, $index) use ($colors, $total) {
            return [
                'label' => $item->type_of_scholarship,
                'count' => $item->count,
                'pct'   => $total > 0 ? round($item->count / $total * 100) : 0,
                'color' => $colors[$index % count($colors)],
            ];
        });

        return [
            'total'          => $total,
            'active'         => $active,
            'inactive'       => $inactive,
            'typeCount'      => $types->count(),
            'typesWithColor' => $typesWithColor,
            'chartData' => json_encode([
                'labels' => $typesWithColor->pluck('label')->values()->toArray(),
                'counts' => $typesWithColor->pluck('count')->values()->toArray(),
                'colors' => $typesWithColor->pluck('color')->values()->toArray(),
            ]),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->isAdmin() || auth()->user()->isScholarship();
    }
}