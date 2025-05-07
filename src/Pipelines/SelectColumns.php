<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Table\Table;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class SelectColumns
{
    /**
     * Select the columns to be displayed.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        if (! $table->isSelectable()) {
            return $next($table);
        }

        $selects = [];
        $resource = $table->getResource();

        foreach ($table->getCachedColumns() as $column) {
            if ($column->isSelectable()) {
                $selecting = $column->getSelects();

                $select = \array_map(
                    static fn ($select) => $column
                        ->qualifyColumn($select, $resource),
                    Arr::wrap($selecting)
                );

                $selects = \array_merge($selects, $select);
            }
        }

        $selects = \array_unique(Arr::flatten($selects), SORT_STRING);

        $resource->select($selects);

        return $next($table);
    }
}
