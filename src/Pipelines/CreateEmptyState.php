<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
 */
class CreateEmptyState
{
    /**
     * Create the empty state of the table considering the refiners, filters and search.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        return $next($table);
    }
}
