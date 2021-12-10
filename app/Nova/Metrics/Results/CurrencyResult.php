<?php

namespace App\Nova\Metrics\Results;

use Laravel\Nova\Metrics\ValueResult;

class CurrencyResult extends ValueResult
{
    public static function fromValueResult(ValueResult $result): static
    {
        return tap(new static($result->value), function ($instance) use ($result) {
            $instance->value = abs($result->value / 100);
            $instance->previous = abs($result->previous / 100);
            $instance->previousLabel = $result->previousLabel;
            $instance->format = '0,0';
            $instance->prefix = '€';
            $instance->suffix = null;
            $instance->suffixInflection = $result->suffixInflection;
            $instance->zeroResult = $result->zeroResult;
        });
    }
}
