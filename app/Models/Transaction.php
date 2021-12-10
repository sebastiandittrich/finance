<?php

namespace App\Models;

use App\Casts\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = ['recipient', 'booked_at', 'valued_at', 'text', 'reason', 'currency', 'amount'];
    protected $casts = [
        'booked_at' => 'date',
        'valued_at' => 'date',
        'total_share' => 'integer',
        // 'amount' => Money::class,
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function costcenters()
    {
        return $this
            ->belongsToMany(CostCenter::class)
            ->using(CostCenterTransaction::class)
            ->selectRaw('cost_centers.*, round((cost_center_transaction.share / shares.total_share) * ? ) as pivot_amount', [$this->getRawOriginal('amount')])
            ->withPivot('share')
            ->joinSub(CostCenterTransaction::totalSharesQuery(), 'shares', 'shares.transaction_id', '=', 'cost_center_transaction.transaction_id');
    }
}
