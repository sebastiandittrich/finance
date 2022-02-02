<?php

namespace App\Nova;

use App\Models\Transaction as ModelsTransaction;
use App\Nova\Actions\AddToCostCenter;
use App\Nova\Actions\ImportFromIng;
use App\Nova\Filters\CostCenters;
use App\Nova\Filters\TransactionType;
use App\Nova\Metrics\AmountTrend;
use App\Nova\Metrics\TotalAmount;
use Brick\Money\Money;
use Illuminate\Http\Request;
use Laravel\Nova\Fields\BelongsTo;
use Laravel\Nova\Fields\BelongsToMany;
use Laravel\Nova\Fields\Currency;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\MorphTo;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Fields\Textarea;
use Ninjalabs\SimpleTagField\SimpleTagField;

class Transaction extends Resource
{
    /**
     * The model the resource corresponds to.
     *
     * @var string
     */
    public static $model = \App\Models\Transaction::class;

    protected static $orderBy = [
        'valued_at' => 'desc',
        'id' => 'desc'
    ];

    /**
     * The single value that should be used to represent the resource when being displayed.
     *
     * @var string
     */
    public function title()
    {
        return Money::ofMinor($this->amount, $this->currency)->__toString();
    }
    public function subtitle()
    {
        return "$this->recipient ({$this->valued_at->format('d. M Y')})";
    }

    /**
     * The columns that should be searched.
     *
     * @var array
     */
    public static $search = [
        'id', 'recipient', 'text', 'reason', 'amount'
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
            Text::make(__('Text'), 'text')->sortable(),
            Text::make(__('Recipient'), 'recipient')->sortable(),
            Textarea::make(__('Reason'), 'reason')->shouldShow(fn () => true),
            Date::make(__('Booked At'), 'booked_at')->hideFromIndex(),
            Date::make(__('Valued At'), 'valued_at')->sortable(),
            Currency::make(__('Amount'), 'amount')->currency($this->currency ?? 'EUR')->asMinorUnits()->sortable()->exceptOnForms(),
            Text::make(__('Currency'), 'currency')->onlyOnForms(),
            Number::make(__('Amount'), 'amount')->onlyOnForms(),
            BelongsTo::make('Import')->nullable()->hideFromIndex(),
            SimpleTagField::make(__('Cost Center'), function () {
                return $this->costcenters()->pluck('name')->all();
            }),
            BelongsToMany::make(__('CostCenters'), 'costcenters', CostCenter::class)->fields(function () {
                return [
                    Number::make(__('Share'), 'share')->default(1),
                    Currency::make(__('Amount'), 'amount')->currency($this->currency ?? 'EUR')->asMinorUnits()->exceptOnForms(),
                ];
            }),
            BelongsTo::make(__('Virtual Account'), 'virtualAccount', VirtualAccount::class)->nullable(),
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
        return [
            TotalAmount::make(ModelsTransaction::query()->where('amount', '<', 0))->name(__('Total Spent')),
            TotalAmount::make(ModelsTransaction::query()->where('amount', '>', 0))->name(__('Total Made')),
            AmountTrend::make(ModelsTransaction::query())->name(__('Amount Trend')),
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
        return [
            TransactionType::make(),
            CostCenters::make(),
        ];
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
            ImportFromIng::make()->standalone(),
            AddToCostCenter::make(),
        ];
    }
}
