<?php

declare(strict_types=1);

namespace Honed\Table\Pipes;

use Honed\Core\Pipe;
use Illuminate\Support\Str;

/**
 * @template TClass of \Honed\Table\Table
 *
 * @extends Pipe<TClass>
 */
class PrepareColumns extends Pipe
{
    /**
     * Run the prepare columns logic.
     */
    public function run(): void
    {
        $instance = $this->instance;

        foreach ($instance->getColumns() as $column) {
            foreach ($this->prepare($instance) as $method) {
                $this->{'prepare'.Str::studly($method)}($instance, $column);
            }
        }
    }

    /**
     * Prepare the column search state.
     *
     * @param  TClass  $instance
     * @param  \Honed\Table\Columns\Column  $column
     * @return void
     */
    protected function prepareSearch($instance, $column)
    {
        $search = $column->getSearch();

        if ($search) {
            $instance->searches($search);
        }
    }

    /**
     * Prepare the column filter state.
     *
     * @param  TClass  $instance
     * @param  \Honed\Table\Columns\Column  $column
     * @return void
     */
    protected function prepareFilter($instance, $column)
    {
        $filter = $column->getFilter();

        if ($filter) {
            $instance->filters($filter);
        }
    }

    /**
     * Prepare the column sort state.
     *
     * @param  TClass  $instance
     * @param  \Honed\Table\Columns\Column  $column
     * @return void
     */
    protected function prepareSort($instance, $column)
    {
        if (! $column->isActive()) {
            return;
        }

        $sort = $column->getSort();

        if ($sort) {
            $instance->sorts($sort);
        }
    }

    /**
     * Prepare the column query state.
     *
     * @param  TClass  $instance
     * @param  \Honed\Table\Columns\Column  $column
     * @return void
     */
    protected function prepareSelect($instance, $column)
    {
        if (! $column->isActive() || ! $column->isSelectable()) {
            return;
        }

        $selects = $column->getSelects();

        if (empty($selects)) {
            $selects[] = $column->getName();
        }

        $builder = $instance->getBuilder();

        $instance->select(
            array_map(
                static fn ($select) => $column->qualifyColumn($select, $builder),
                $selects
            )
        );
    }

    /**
     * Get the methods to prepare.
     *
     * @param  TClass  $instance
     * @return array<int, string>
     */
    protected function prepare($instance)
    {
        $methods = [];

        foreach ($this->getMethods($instance) as $method => $value) {
            if ($value) {
                $methods[] = $method;
            }
        }

        return $methods;
    }

    /**
     * Get the methods to prepare.
     *
     * @param  TClass  $instance
     * @return array<string, bool>
     */
    protected function getMethods($instance)
    {
        return [
            'search' => $instance->isSearchable(),
            'filter' => $instance->isFilterable(),
            'sort' => $instance->isSortable(),
            'select' => $instance->isSelectable(),
        ];
    }
}
