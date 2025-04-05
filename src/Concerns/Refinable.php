<?php

namespace Honed\Table\Concerns;

trait Refinable
{
    /**
     * Whether the instance is sortable, and the sort itself.
     * 
     * @var \Honed\Refine\Sort<TModel, TBuilder>|null
     */
    protected $sortable;

    /**
     * Whether the instance is searchable.
     *
     * @var bool
     */
    protected $searchable = false;

    /**
     * Whether the instance is filterable.
     *
     * @var bool
     */
    protected $filterable = false;

    /**
     * Whether the instance is selectable, and what to select.
     *
     * @var \Honed\Refine\Select<TModel, TBuilder>|null
     */
    protected $select;
}