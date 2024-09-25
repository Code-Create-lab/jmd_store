<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GatePassOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Gate Pass', '192.1k')
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Gate Pass Amount', '21%'),
            Stat::make('Total Product', '3:12'),
            Stat::make('Total Product Box', '3:12'),
        ];
    }
}
