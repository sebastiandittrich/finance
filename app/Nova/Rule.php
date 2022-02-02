<?php

namespace App\Nova;

use App\Actions\RunRuleNow;
use App\Models\Condition as ModelsCondition;
use App\Models\Rule as ModelsRule;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;

class Rule extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Rule::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id',
    ];

    /**
     * Get the fields displayed by the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make(__('ID'), 'id')->sortable(),
            Text::make(__('Name'), 'name')->sortable(),
            MorphTo::make(__('Target'), 'target', CostCenter::class)->showCreateRelationButton()->types([
                CostCenter::class,
                VirtualAccount::class,
            ]),
            Text::make(__('Human Readable Rule'), function (ModelsRule $rule) {
                return $rule->conditions->map(fn (ModelsCondition $condition) => "$condition->attribute $condition->operator \"$condition->value\"")->implode(' and ');
            })->hideFromIndex(),
            HasMany::make(__('Conditions'), 'conditions', Condition::class),
        ];
    }

    /**
     * Get the cards available for the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the filters available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [];
    }

    /**
     * Get the lenses available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function lenses(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return [
            RunRuleNow::nova()
        ];
    }
}
