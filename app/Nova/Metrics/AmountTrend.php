<?php

namespace App\Nova\Metrics;

use App\Nova\Metrics\Results\CurrencyTrendResult;
use App\Nova\Metrics\Traits\Reusable;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Trend;

class AmountTrend extends Trend
{
    use Reusable;

    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        return CurrencyTrendResult::fromTrendResult(
            $this
                ->sumByMonths($request, ($this->queryResolver)($request), $this->column, 'valued_at')
                ->showSumValue()
        );
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
}
