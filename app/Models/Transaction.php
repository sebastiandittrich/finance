<?php

namespace App\Models;

use App\Casts\Money;
use App\Events\TransactionCreated;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    protected $dispatchesEvents = [
        'created' => TransactionCreated::class,
    ];

    public function import()
    {
        return $this->belongsTo(Import::class);
    }

    public function costcenters(): BelongsToMany
    {
        return $this
            ->belongsToMany(CostCenter::class)
            ->selectRaw('cost_centers.*, round((cost_center_transaction.share / shares.total_share) * ? ) as pivot_amount', [$this->getRawOriginal('amount')])
            ->joinSub(CostCenterTransaction::totalSharesQuery(), 'shares', 'shares.transaction_id', '=', 'cost_center_transaction.transaction_id')
            ->using(CostCenterTransaction::class)
            ->withPivot('id', 'share')
            ->withTimestamps();
    }

    public function virtualAccount()
    {
        return $this->belongsTo(VirtualAccount::class);
    }
}
