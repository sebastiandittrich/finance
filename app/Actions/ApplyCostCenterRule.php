<?php

namespace App\Actions;

use App\Models\CostCenterTransaction;
use App\Models\Rule;
use App\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;

class ApplyCostCenterRule
{
    use AsAction;

    public function handle(Rule $rule, Transaction $transaction)
    {
        if ($transaction->costcenters()->where('cost_center_id', $rule->target->id)->count() == 0) {
            $costCenterTransaction = new CostCenterTransaction();
            $costCenterTransaction->transaction()->associate($transaction);
            $costCenterTransaction->costCenter()->associate($rule->target);
            $costCenterTransaction->originator()->associate($rule);
            $costCenterTransaction->save();
        }
    }
}
