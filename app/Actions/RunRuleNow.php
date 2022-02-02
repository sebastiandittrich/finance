<?php

namespace App\Actions;

use App\Models\Rule;
use App\Models\Transaction;
use App\NovaActions\AsNovaAction;
use Lorisleiva\Actions\Concerns\AsAction;

class RunRuleNow
{
    use AsAction, AsNovaAction;

    public function handle(Rule $rule)
    {
        $transactions = Transaction::all();

        foreach ($transactions as $transaction) {
            $rule->executeIfMatches($transaction);
        }
    }
}
