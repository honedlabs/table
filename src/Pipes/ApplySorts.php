<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Closure;
use Honed\Table\Pipes\Contracts\Sorts;
use Honed\Table\Table;

/**
 * @internal
 */
class ApplySorts implements Sorts
{
    public function handle(Table $table, Closure $next)
    {
        $builder = $table->getResource();

        $sorts = array_merge(
            $table->getSorts(),
            // $table->getSortableColumns()->map(fn ($column) => $column->getSort())->toArray()
        );

        // Get a single source of truth for the sort name and direction
        [$sortBy, $direction] = $table->getSortBy();

        foreach ($sorts as $sort) {
            $sort->apply($builder, $sortBy, $direction);
        }

        $table->setResource($builder);

        return $next($table);
    }
}
