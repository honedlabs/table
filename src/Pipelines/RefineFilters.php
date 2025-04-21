<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Refine\Filter;
use Honed\Refine\Pipelines\RefineFilters as BaseRefineFilters;
use Honed\Table\Columns\Column;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends BaseRefineFilters<TModel, TBuilder>
 */
class RefineFilters extends BaseRefineFilters
{
    /**
     * The filters to use.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return array<int, \Honed\Refine\Filter<TModel, TBuilder>>
     */
    public function filters($table)
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

        $table->filters($filters);

        return parent::filters($table);
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
            'number' => $filter->int(),
            'text' => $filter->text(),
            default => null,
        };

        return $filter;
    }
}
