<?php

namespace App\Actions;

use App\Models\Rule;
use App\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;

class ApplyVirtualAccountRule
{
    use AsAction;

    public function handle(Rule $rule, Transaction $transaction)
    {
        $transaction->virtualAccount()->associate($rule->target)->save();
    }
}
