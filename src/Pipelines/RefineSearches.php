<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Refine\Pipelines\RefineSearches as BaseRefineSearches;
use Honed\Refine\Search;
use Honed\Table\Columns\Column;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends BaseRefineSearches<TModel, TBuilder>
 */
class RefineSearches extends BaseRefineSearches
{
    /**
     * The searches to use.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return array<int, \Honed\Refine\Search<TModel, TBuilder>>
     */
    public function searches($table)
    {
        $columns = $table->getColumns();

        /** @var array<int,\Honed\Refine\Search<TModel, TBuilder>> */
        $searches = \array_map(
            static fn (Column $column) => Search::make($column->getName(), $column->getLabel())
                ->alias($column->getParameter()),
            \array_values(
                \array_filter(
                    $columns,
                    static fn (Column $column) => $column->isSearchable()
                )
            )
        );

        $table->searches($searches);

        return parent::searches($table);
    }
}
