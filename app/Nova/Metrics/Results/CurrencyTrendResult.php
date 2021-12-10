<?php

namespace App\Nova\Metrics\Results;

use Laravel\Nova\Metrics\TrendResult;

class CurrencyTrendResult extends TrendResult
{
    public static function fromTrendResult(TrendResult $result)
    {
        return tap(new static, function (CurrencyTrendResult $instance) use ($result) {
            $instance
                ->prefix('€')
                ->trend(array_map(fn (int $value) => $value / 100, $result->trend))
                ->result($result->value / 100)
                ->format('0,0.00');
        });
    }
}
