<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class QueryColumns
{
    /**
     * Apply the column callbacks to the query.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        $builder = $table->getResource();

        foreach ($table->getCachedColumns() as $column) {
            $column->modifyQuery($builder);
        }

        return $next($table);
    }
}
