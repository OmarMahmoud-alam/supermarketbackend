<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Custom;
use App\Models\Product;
use App\Enums\OrderStatusEnum;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class StatusOverview extends BaseWidget
{ 
    protected static ?int $sort = 2;
    protected static ?string $pollingInterval = '15s';

    protected static bool $isLazy = true;
    protected function getStats(): array
    {
        return [
            Stat::make('Total Customers', Custom::count())
            ->description('Increase in customers')
            ->descriptionIcon('heroicon-m-arrow-trending-up')
            ->color('success'),
           // ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
          /*  Stat::make('verified custom', Custom::orWhereNull('email_verified_at' )->count())
            ->description('Total verfied custom in app')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart([7, 3, 4, 5, 6, 3, 5, 3]), */
        Stat::make('Total Products', Product::count())
            ->description('Total products in app')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart([7, 3, 4, 5, 6, 3, 5, 3]),
        Stat::make('Pending Orders', Order::where('status', OrderStatusEnum::PENDING->value)->count())
            ->description('Total products in app')
            ->descriptionIcon('heroicon-m-arrow-trending-down')
            ->color('danger')
            ->chart([7, 3, 4, 5, 6, 3, 5, 3]),        ];
    }
}
