<?php

namespace App\Nova\Metrics\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Laravel\Nova\Actions\CallQueuedAction;
use Laravel\Nova\Metrics\Metric;

trait Reusable
{
    protected $queryResolver;
    protected $uriKey;
    protected $cacheFor;

    public function __construct(Builder|callable $query, public string $column = 'amount')
    {
        parent::__construct();

        $this->queryResolver = $query instanceof Builder ? fn () => $query : $query;
    }

    public function name(string|callable $name = null): string|self
    {
        return $this->getOrSet('name', $name, fn () => parent::name());
    }

    /**
     * Get the URI key for the metric.
     */
    public function uriKey(string|callable $uriKey = null): string|self
    {
        return $this->getOrSet('uriKey', $uriKey, fn () => Str::slug($this->name));
    }

    public function getOrSet(string $attribute, mixed $value, callable $default)
    {
        if (!is_null($value)) {
            $this->$attribute = is_callable($value) ? $value($this) : $value;
            return $this;
        }
        return $this->$attribute ?? $default();
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int|self
     */
    public function cacheFor(\DateTimeInterface|\DateInterval|float|int|callable $cacheFor = null)
    {
        return $this->getOrSet('cacheFor', $cacheFor, fn () => parent::cacheFor());
    }
}
