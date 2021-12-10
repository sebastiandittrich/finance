<?php

namespace App\Nova\Actions;

use App\Models\CostCenter;
use App\Nova\CostCenter as NovaCostCenter;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Nova;

class AddToCostCenter extends Action
{
    use InteractsWithQueue, Queueable;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $costCenter = CostCenter::find($fields->cost_center);
        $costCenter->transactions()->syncWithoutDetaching($models->pluck('id'));

        return Action::message("Added {$models->count()} transaction to {$costCenter->name}");
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Select::make('Cost Center')->options(
                CostCenter::all()
                    ->map(fn (CostCenter $costCenter) => new NovaCostCenter($costCenter))
                    ->keyBy(fn (NovaCostCenter $costCenter) => $costCenter->model()->id)
                    ->map->title()
            )
        ];
    }
}
