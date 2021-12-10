<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class CostCenterTransaction extends Pivot
{
    protected $casts = [
        // 'amount' => Money::class,
    ];

    public static function totalSharesQuery()
    {
        return DB::table('cost_center_transaction')
            ->selectRaw('transaction_id, sum(`share`) as total_share')
            ->join('transactions', 'cost_center_transaction.transaction_id', '=', 'transactions.id')
            ->groupBy('transaction_id');
    }
}
