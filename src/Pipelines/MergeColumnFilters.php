<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Refine\Filter;
use Honed\Table\Columns\Column;
use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class MergeColumnFilters
{
    /**
     * Merge the column filters with the table.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        $columns = $table->getColumns();

        /** @var array<int,\Honed\Refine\Filter<TModel, TBuilder>> */
        $filters = \array_map(
            static fn (Column $column) => static::createFilter($column),
            \array_values(
                \array_filter(
                    $columns,
                    static fn (Column $column) => $column->isFilterable()
                )
            )
        );

        $table->withFilters($filters);

        return $next($table);
    }

    /**
     * Extract the type to use for the filter.
     *
     * @param  \Honed\Table\Columns\Column<TModel, TBuilder>  $column
     * @return \Honed\Refine\Filter<TModel, TBuilder>
     */
    public static function createFilter(Column $column)
    {
        $filter = Filter::make($column->getName(), $column->getLabel())
            ->alias($column->getParameter());

        $type = $column->getType();

        match ($type) {
            'date' => $filter->date(),
            'boolean' => $filter->boolean(),
            'number' => $filter->integer(),
            'text' => $filter->string(),
            default => null,
        };

        return $filter;
    }
}
