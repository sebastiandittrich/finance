<?php

namespace App\Nova\Metrics;

use App\Models\CostCenter;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Metrics\Partition;

class CostCenters extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @return mixed
     */
    public function calculate(NovaRequest $request)
    {
        if ($request->resourceId) {
            $model = CostCenter::find($request->resourceId);
            $children = $model->children;
        } else {
            $children = CostCenter::isRoot()->get();
        }
        $result = collect($children)->mapWithKeys(fn (CostCenter $costCenter) => [
            $costCenter->name => abs(intval(DB::table($costCenter->transactionsOfDescendantsAndSelf(), 'all_transactions')->sum('pivot_amount'))) / 100
        ]);
        if (isset($model)) {
            $result = $result->merge([
                $model->name => abs(intval(DB::table($model->transactions(), 'all_transactions')->sum('pivot_amount'))) / 100
            ]);
        }
        return $this->result($result->all());
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
        return 'cost-centers';
    }
}
