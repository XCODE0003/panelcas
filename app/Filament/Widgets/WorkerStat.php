<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\User;
use App\Models\Deposit;
use App\Models\Promo;
use App\Models\PromoActivation;
use Illuminate\Support\Facades\Http;

class WorkerStat extends BaseWidget
{
    protected function getStats(): array
    {
        $referrals = User::where('inviter', auth()->user()->id)->get();
        $deposits = Deposit::whereIn('user_id', $referrals->pluck('id'))->get();
        $total_deposits = $deposits->sum('amount');
        $total_amount_deposits = 0;
        $total_referrals = $referrals->count();
        $promocodes = Promo::where('user_id', auth()->user()->id)->get();
        $total_promocodes = $promocodes->count();
        $total_activation = PromoActivation::whereIn('promo_id', $promocodes->pluck('id'))->get();
        $total_activation = $total_activation->count();

        try {
            $course_btc = Http::withoutVerifying()
                ->get('https://api.coindesk.com/v1/bpi/currentprice/BTC.json')
                ->json()['bpi']['USD']['rate_float'];
    
            // Используем CoinGecko API для ETH
            $eth_response = Http::withoutVerifying()
                ->get('https://api.coingecko.com/api/v3/simple/price?ids=ethereum&vs_currencies=usd')
                ->json();
    
            if (!isset($eth_response['ethereum']) || !isset($eth_response['ethereum']['usd'])) {
                throw new \Exception('Invalid ETH API response structure');
            }
    
            $course_eth = $eth_response['ethereum']['usd'];
    
        } catch (\Exception $e) {
            \Log::error('Error getting crypto rates: ' . $e->getMessage());
            $course_btc = 41000;
            $course_eth = 2200;  
        }
        foreach ($deposits as $deposit) {
            if($deposit->currency == 'BTC'){
                $total_amount_deposits += $deposit->amount * $course_btc;
            }
            if($deposit->currency == 'ETH'){
                $total_amount_deposits += $deposit->amount * $course_eth;
            }
            if($deposit->currency == 'USDT'){
                $total_amount_deposits += $deposit->amount * 1;
            }
        }

        return [
            Stat::make('Общая сумма депозитов', $total_amount_deposits . ' $'),
        ];
    }
}
