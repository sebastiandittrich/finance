<?php

namespace App\Models;

use App\Actions\ApplyCostCenterRule;
use App\Actions\ApplyVirtualAccountRule;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rule extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function target()
    {
        return $this->morphTo('rulable');
    }

    public function conditions()
    {
        return $this->hasMany(Condition::class);
    }

    public function matches(Transaction $transaction): bool
    {
        return $this->conditions->every(fn (Condition $rule) => $rule->matches($transaction));
    }

    public function executeIfMatches(Transaction $transaction)
    {
        if ($this->matches($transaction)) {
            $this->execute($transaction);
        }
    }

    public function execute(Transaction $transaction)
    {
        match ($this->target::class) {
            CostCenter::class => ApplyCostCenterRule::run($this, $transaction),
            VirtualAccount::class => ApplyVirtualAccountRule::run($this, $transaction),
        };
    }
}
