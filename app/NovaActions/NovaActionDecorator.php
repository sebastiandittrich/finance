<?php

namespace App\NovaActions;

use Illuminate\Database\Eloquent\Collection;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Fields\ActionFields;

class NovaActionDecorator extends Action
{
    public function __construct(public $action)
    {
    }

    public function name(): string
    {
        return $this->action->name();
    }

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        return $this->action->asNovaAction($fields, $models);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return $this->action->novaActionFields();
    }
}
