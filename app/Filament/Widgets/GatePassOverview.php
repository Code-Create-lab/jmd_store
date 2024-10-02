<?php

namespace App\Filament\Widgets;

use App\Models\GatePass;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class GatePassOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $gatePass = new GatePass();
        $totalGatePass = $gatePass->count();
        $totalGatePassAmount = $gatePass->sum('total_amount');

        $product = new Product();
        $totalProduct = $product->count();
        $totalProductBox = $product->sum('box');
        return [
            Stat::make('Total Gate Pass', $totalGatePass)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Gate Pass Amount', '₹' . $totalGatePassAmount)
                ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Product', $totalProduct),
            Stat::make('Total Product Box', $totalProductBox),
        ];
    }
}
