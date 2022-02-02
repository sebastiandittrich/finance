<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Condition extends Model
{
    use HasFactory;

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function matches(Transaction $transaction)
    {
        return match ($this->operator) {
            '=' => $transaction->getAttribute($this->attribute) == $this->value,
            'contains' => Str::contains($transaction->getAttribute($this->attribute), $this->value),
            default => throw new Exception("Invalid operator, {$this->operator}"),
        };
    }

    public function costCenterTransactions()
    {
        return $this->morphMany(Transaction::class, 'originator');
    }

    public function rule()
    {
        return $this->belongsTo(Rule::class);
    }
}
