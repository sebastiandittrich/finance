<?php

namespace App\NovaActions;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\App;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Nova;

trait AsNovaAction
{
    public ?string $name = null;

    public static function nova(...$args)
    {
        return new NovaActionDecorator(new static(...$args));
    }

    public function asNovaAction(ActionFields $fields, Collection $models)
    {
        $models->each(fn ($model) => $this->handle($model));
    }

    public function novaActionFields()
    {
        return [];
    }

    public function name(): string
    {
        return $this->name ?: Nova::humanize($this);
    }
}
