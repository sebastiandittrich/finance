<?php

namespace App\Actions;

use App\Events\TransactionCreated;
use App\Models\Rule;
use App\Models\Transaction;
use Lorisleiva\Actions\Concerns\AsAction;

class AssignCostCentersByRules
{
    use AsAction;

    public function handle(Transaction $transaction)
    {
        $rules = Rule::all();
        $rules->each(fn (Rule $rule) => $rule->executeIfMatches($transaction));
    }

    public function asListener(TransactionCreated $event)
    {
        return $this->handle($event->transaction);
    }
}
