<?php

namespace App\Nova;

use App\Models\CostCenter as ModelsCostCenter;
use App\Models\Transaction;
use App\Nova\Metrics\AmountTrend;
use App\Nova\Metrics\CostCenters;
use App\Nova\Transaction as NovaTransaction;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\HasMany;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphMany;
use Laravel\Nova\Fields\Text;

class CustomBuilder extends EloquentBuilder
{
    public function setModel(Model $model)
    {
        $this->model = $model;
        return $this;
    }
}

class CostCenter extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\CostCenter::class;

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public static $title = 'name';

    public function title()
    {
        return $this->ancestorsAndSelf()->orderBy('depth', 'ASC')->get()->implode('name', ' / ');
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'name',
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
            BelongsToMany::make('Transactions', 'transactions', NovaTransaction::class)->fields(function () {
                return [
                    Currency::make(__('Shared Amount'), 'amount')->currency($this->currency ?? 'EUR')->asMinorUnits()->readonly()
                ];
            }),
            BelongsTo::make('Parent', 'parent', CostCenter::class)->nullable(),
            HasMany::make('Children', 'children', CostCenter::class),
            MorphMany::make('Rules', 'rules', Rule::class),
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
        $wrap = fn ($query) => (new CustomBuilder(DB::table($query, 'amounts')))->setModel(Transaction::make());
        $model = fn () => ModelsCostCenter::find($request->resourceId);
        return [
            AmountTrend::make(fn () => $wrap($model()->transactionsOfDescendantsAndSelf()), 'pivot_amount')
                ->onlyOnDetail()
                ->name('Amount Spent')
                ->uriKey(fn () => "cost-center-spent-{$request->resourceId}")
                ->defaultRange(12),
            CostCenters::make(),
            CostCenters::make()->onlyOnDetail()->canSee(fn ($request) => $model() && $model()->children->count()),
        ];
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
