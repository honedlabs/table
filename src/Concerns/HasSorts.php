<?php

namespace Conquest\Table\Concerns;

use Conquest\Table\Sorts\BaseSort;

/**
 * @mixin Conquest\Table\Concerns\HasOrder
 * @mixin Conquest\Table\Concerns\HasSort
 */
trait HasSorts
{
    /**
     * @var array<int, BaseSort>
     */
    protected $sorts;

    /**
     * @param array<int, Conquest\Table\Sorts\BaseSort>|null $sorts
     */
    protected function setSorts($sorts)
    {
        if (is_null($sorts)) {
            return;
        }
        $this->sorts = $sorts;
    }


    /**
     * @internal
     * @return array<int, Conquest\Table\Sorts\BaseSort>
     */
    protected function definedSorts()
    {
        if (isset($this->sorts)) {
            return $this->sorts;
        }

        if (method_exists($this, 'sorts')) {
            return $this->sorts();
        }

        return [];
    }

    /**
     * Get the authorized sorts.
     * 
     * @return array<int, Conquest\Table\Sorts\BaseSort>
     */
    public function getSorts()
    {
        return array_filter($this->definedSorts(), fn (BaseSort $sort) => $sort->isAuthorized());
    }

    /**
     * Apply the authorized sorts to the builder.
     * 
     * @param Illuminate\Database\Eloquent\Builder $builder
     */
    protected function sort($builder)
    {
        foreach ($this->getSorts() as $sort) {
            $sort->apply($builder);
        }
    }
}
