<?php

namespace App\Nova\Metrics;

use App\Models\Transaction;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;
use Laravel\Nova\Metrics\TrendResult;

// class CurrencyTrendResult extends TrendResult
// {
//     public static function fromTrendResult(TrendResult $result)
//     {
//         return tap(new static, function (CurrencyTrendResult $instance) use ($result) {
//             $instance->prefix('€')->trend($result->trend);
//         });
//     }
// }

class Spent extends Trend
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return $this->sumByMonths($request, Transaction::query(), 'amount', 'valued_at');
    }

    /**
     * Get the ranges available for the metric.
     *
     * @return array
     */
    public function ranges()
    {
        return [
            6 => __('6 Months'),
            12 => __('12 Months'),
            24 => __('24 Months'),
        ];
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'spent';
    }
}
