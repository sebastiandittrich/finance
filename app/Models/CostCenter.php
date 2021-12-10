<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\LaravelAdjacencyList\Eloquent\HasRecursiveRelationships;

class CostCenter extends Model
{
    use HasFactory;
    use HasRecursiveRelationships;

    public function transactions()
    {
        return $this
            ->belongsToMany(Transaction::class)
            ->using(CostCenterTransaction::class)
            ->withPivot('share')
            ->selectRaw('transactions.*, floor((cost_center_transaction.share / shares.total_share) * transactions.amount) as pivot_amount')
            ->joinSub(CostCenterTransaction::totalSharesQuery(), 'shares', 'shares.transaction_id', '=', 'cost_center_transaction.transaction_id');
    }

    public function transactionsOfDescendantsAndSelf()
    {
        return $this
            ->belongsToManyOfDescendantsAndSelf(Transaction::class)
            ->using(CostCenterTransaction::class)
            ->withPivot('share')
            ->selectRaw('transactions.*, floor((cost_center_transaction.share / shares.total_share) * transactions.amount) as pivot_amount')
            ->joinSub(CostCenterTransaction::totalSharesQuery(), 'shares', 'shares.transaction_id', '=', 'cost_center_transaction.transaction_id');
    }
}
