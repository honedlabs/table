<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Contracts\IsViewable;
use Honed\Table\Facades\Views;
use Honed\Table\PendingViewInteraction;

trait Viewable
{
    /**
     * The table views to utilise.
     *
     * @var bool|array<int, mixed>
     */
    protected $viewable = false;

    /**
     * Set whether the table is viewable, or the specific views you want to use.
     *
     * @param  mixed|array<int, mixed>  $scope
     * @return $this
     */
    public function viewable($scope = true)
    {
        $this->viewable = match (true) {
            is_bool($scope) => $scope,
            default => is_array($scope) ? $scope : func_get_args()
        };

        return $this;
    }

    /**
     * Set whether the table is not viewable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notViewable($value = true)
    {
        $this->viewable = ! $value;

        return $this;
    }

    /**
     * Determine if the table has views.
     *
     * @return bool
     */
    public function isViewable()
    {
        return (bool) $this->viewable || $this instanceof IsViewable;
    }

    /**
     * Determine if the table does not have views.
     *
     * @return bool
     */
    public function isNotViewable()
    {
        return ! $this->isViewable();
    }

    /**
     * Get the views for the table.
     *
     * @return PendingViewInteraction|null
     */
    public function getViews()
    {
        return match (true) {
            ! $this->isViewable() => null,
            is_array($this->viewable) => Views::for($this->viewable),
            default => Views::for(),
        };
    }

    /**
     * Load the views for the table.
     *
     * @return array<int, object>|null
     */
    public function listViews()
    {
        return $this->getViews()?->list($this);
    }
}
