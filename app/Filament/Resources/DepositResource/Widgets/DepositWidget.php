<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use App\Models\Deposit;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Promo;
use App\Models\PromoActivation;
class DepositWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $referrals = User::where('inviter', auth()->user()->id)->get();
        $deposits = Deposit::whereIn('user_id', $referrals->pluck('id'))->get();
        $total_deposits = $deposits->sum('amount');
        $total_amount_deposits = $deposits->sum('amount');
        $total_referrals = $referrals->count();
        $promocodes = Promo::where('user_id', auth()->user()->id)->get();
        $total_promocodes = $promocodes->count();
        $total_activation = PromoActivation::whereIn('promo_id', $promocodes->pluck('id'))->get();
        $total_activation = $total_activation->count();

        return [
            Stat::make('Всего рефералов', $total_referrals),
            Stat::make('Всего депозитов', $total_deposits),
            // ->description('Cумма депозитов: ' . $total_amount_deposits . ' $'),
            Stat::make('Всего промокодов', $total_promocodes),
            Stat::make('Всего активаций', $total_activation)

        ];
    }
}

