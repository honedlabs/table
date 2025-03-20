<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Refine\Pipelines\RefineSorts as BaseRefineSorts;
use Honed\Table\Columns\Column;
use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends BaseRefineSorts<TModel, TBuilder>
 */
class RefineSorts extends BaseRefineSorts
{
    /**
     * Apply the sorts refining logic.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        if (! $table->isSorting()) {
            return $next($table);
        }

        $request = $table->getRequest();
        $for = $table->getFor();

        $sortsKey = $table->formatScope($table->getSortsKey());

        $value = $this->nameAndDirection($request, $sortsKey);

        /** @var array<int,\Honed\Refine\Sort<TModel, TBuilder>> */
        $sorts = \array_merge($table->getSorts(),
            \array_map(
                static fn (Column $column) => $column->getSort(),
                \array_values(
                    \array_filter(
                        $table->getColumns(),
                        static fn (Column $column) => $column->isSortable()
                    )
                )
            )
        );

        $applied = false;

        foreach ($sorts as $sort) {
            $applied |= $sort->refine($for, $value);
        }

        if (! $applied && $sort = $table->getDefaultSort()) {
            [$_, $direction] = $value;

            $value = [$sort->getParameter(), $direction];

            $sort->refine($for, $value);
        }

        return $next($table);
    }
}
