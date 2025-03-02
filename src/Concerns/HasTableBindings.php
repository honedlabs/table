<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Table;

trait HasTableBindings
{
    /**
     * @return string
     */
    public function getRouteKey()
    {
        return Table::encode(static::class);
    }

    /**
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'table';
    }

    /**
     * @param  mixed  $value
     * @param  string|null  $field
     * @return \Honed\Table\Table<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>|null
     */
    public function resolveRouteBinding($value, $field = null)
    {
        try {
            $class = Table::decode($value);

            if (! \class_exists($class) || ! \is_subclass_of($class, Table::class)) {
                return null;
            }

            return $class::make();
        } catch (\Throwable $th) {
            return null;
        }
    }

    /**
     * @param  string  $childType
     * @param  string  $value
     * @param  string|null  $field
     * @return \Honed\Table\Table<\Illuminate\Database\Eloquent\Model, \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>>|null
     */
    public function resolveChildRouteBinding($childType, $value, $field)
    {
        return $this->resolveRouteBinding($value, $field);
    }
}
