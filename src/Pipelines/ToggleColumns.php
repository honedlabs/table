<?php

declare(strict_types=1);

namespace Honed\Table\Pipelines;

use Honed\Core\Interpret;
use Honed\Table\Columns\Column;
use Honed\Table\Table;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
class ToggleColumns
{
    /**
     * Toggle the columns that are displayed.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  \Closure(Table<TModel, TBuilder>): Table<TModel, TBuilder>  $next
     * @return \Honed\Table\Table<TModel, TBuilder>
     */
    public function __invoke($table, $next)
    {
        if (! $table->isToggleable()) {

            static::cacheColumns($table);

            return $next($table);
        }

        $request = $table->getRequest();

        $params = Interpret::array(
            $request,
            $table->formatScope($table->getColumnKey()),
            $table->getDelimiter(),
            'string'
        );

        if ($table->isRememberable()) {
            $params = $table->configureCookie($request, $params);
        }

        static::cacheColumns($table, $params);

        return $next($table);
    }

    /**
     * Cache the columns to be displayed.
     *
     * @param  \Honed\Table\Table<TModel, TBuilder>  $table
     * @param  array<int,string>|null  $params
     * @return void
     */
    public static function cacheColumns($table, $params = null)
    {
        $table->cacheColumns(
            \array_values(
                \array_filter(
                    $table->getColumns(),
                    static fn (Column $column) => $column->visible($params)
                )
            )
        );
    }
}
