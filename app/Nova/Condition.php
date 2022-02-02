<?php

namespace App\Nova;

use App\Models\Condition as ModelsCondition;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;

class Condition extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Condition::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public function title()
    {
        return "$this->attribute $this->operator $this->value";
    }

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
            Select::make(__('Attribute'), 'attribute')->options([
                'recipient' => 'Recipient',
                'text' => 'Text',
                'amount' => 'Amount',
                'reason' => 'Reason',
            ])->displayUsingLabels()->hideFromIndex(),
            Select::make(__('Operator'), 'operator')->options([
                '=' => '=',
                'contains' => 'contains',
            ])->hideFromIndex(),
            Text::make(__('Value'), 'value')->hideFromIndex(),
            Text::make(__('Human Readable'), function (ModelsCondition $condition) {
                return "$condition->attribute $condition->operator \"$condition->value\"";
            }),
            BelongsTo::make(__('Rule'), 'rule', Rule::class)->showCreateRelationButton(),
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
        return [];
    }
}
