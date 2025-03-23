<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Refine\Pipelines\RefineSorts as BaseRefineSorts;
use Honed\Table\Columns\Column;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends BaseRefineSorts<TModel, TBuilder>
 */
class RefineSorts extends BaseRefineSorts
{
    /**
     * The sorts to use.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @return array<int, \Honed\Refine\Sort<TModel, TBuilder>>
     */
    public function sorts($table)
    {
        /** @var array<int,\Honed\Refine\Sort<TModel, TBuilder>> */
        $sorts = \array_map(
            static fn (Column $column) => $column->getSort(),
            \array_values(
                \array_filter(
                    $table->getColumns(),
                    static fn (Column $column) => $column->isSortable()
                )
            )
        );

        return \array_merge($table->getSorts(), $sorts);
    }
}
