<?php

namespace App\Table\Pipes;

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
            $table->getSortableColumns()->map(fn ($column) => $column->getSort())->toArray()
        );
        foreach ($sorts as $sort) {
            $sort->apply($builder);
        }
        $table->setResource($builder);

        return $next($table);
    }
}
