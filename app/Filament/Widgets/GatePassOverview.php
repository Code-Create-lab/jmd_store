<?php

namespace App\Filament\Widgets;

use App\Models\GatePass;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class GatePassOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $gatePass = new GatePass();
        $totalGatePass = $gatePass->count();
        $totalGatePassAmount = $gatePass->sum('total_amount');
        $totalGatePassAmountArr = $gatePass->pluck('total_amount')->toArray();


        $product = new Product();
        $totalProduct = $product->sum('remaining_box');
        $totalProductBox = $product->sum('box');
        $totalProductArr = $product->pluck('box')->toArray();

        // $data = Trend::query(Product::where("status", 1))
        //     ->dateColumn('created_at')
        //     ->between(
        //         start: now()->startOfMonth(),
        //         end: now()->endOfMonth(),
        //     )
        //     ->perMonth()
        //     ->count();

        // $data = Trend::model(Product::class)
        //     ->dateColumn('date')
        //     ->between(
        //         start: now()->startOfYear(),
        //         end: now()->endOfYear(),
        //     )
        //     ->perMonth()
        //     ->count();

        // // dd($totalProductArr);

        return [
            Stat::make('Total Gate Pass', $totalGatePass)
                // ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')->chart($totalProductArr),
            Stat::make('Total Gate Pass Amount', '₹' . $totalGatePassAmount)
                // ->description('32k increase')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success')
                ->chart($totalGatePassAmountArr),
            Stat::make('Total Product Box', $totalProductBox),
            Stat::make('Total Remaining Product', $totalProduct),
        ];
    }
}
