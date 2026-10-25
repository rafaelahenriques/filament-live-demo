<?php

namespace App\Filament\Widgets;

use App\Models\Vote;
use chillerlan\QRCode\QRCode;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VoteStats extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '2s';

    protected function getStats(): array
    {
        $qr = (new QRCode)->render(url('/admin/votes?tableAction=create'));

        return [
            Stat::make('Log in: demo@filament.test / filament', str("<img src='{$qr}'>")->toHtmlString()),
            ...collect(Vote::VERDICTS)->map(fn (string $label, string $verdict) => Stat::make($label, Vote::where('verdict', $verdict)->count())),
        ];
    }
}
