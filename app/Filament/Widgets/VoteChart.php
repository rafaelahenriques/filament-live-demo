<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use Filament\Widgets\ChartWidget;

class VoteChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $heading = 'Would you use Filament?';

    protected ?string $pollingInterval = '2s';

    protected ?string $maxHeight = '260px';

    protected function getData(): array
    {
        return [
            'datasets' => [[
                'label' => 'Votes',
                'data' => collect(Vote::VERDICTS)->keys()->map(fn (string $verdict) => Vote::where('verdict', $verdict)->count())->all(),
                'backgroundColor' => ['#8f7cf7', '#c5e384', '#52525b'],
            ]],
            'labels' => array_values(Vote::VERDICTS),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
