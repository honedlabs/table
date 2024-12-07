<?php

namespace Honed\Table\Concerns;

use Honed\Table\Actions\BaseAction;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Actions\PageAction;
use Illuminate\Support\Collection;

/**
 * @mixin \Honed\Core\Concerns\Inspectable
 */
trait HasActions
{
    /**
     * @var array<int,\Honed\Table\Actions\BaseAction>
     */
    protected $actions;

    /**
     * Set the actions for the table.
     *
     * @param  array<int,\Honed\Table\Actions\BaseAction>|null  $actions
     */
    public function setActions($actions): void
    {
        if (\is_null($actions)) {
            return;
        }

        $this->actions = $actions;
    }

    /**
<<<<<<< HEAD
=======
     * Get the actions for the table.
     *
     * @internal
     *
     * @return array<int, Conquest\Table\Actions\BaseAction>
     */
    public function definedActions(): array
    {
        if (isset($this->actions)) {
            return $this->actions;
        }

        if (method_exists($this, 'actions')) {
            return $this->actions();
        }

        return [];
    }

    /**
>>>>>>> 273ec095ac1a6c447503fb3d57a814c10354ca4c
     * Get all available actions.
     *
     * @return Collection<int,\Honed\Table\Actions\BaseAction>
     */
    public function getActions(): Collection
    {
        return collect($this->inspect('actions', []));
    }

    /**
     * Get the inline actions.
     *
     * @return Collection<int,\Honed\Table\Actions\InlineAction>
     */
    public function getInlineActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (BaseAction $action): bool => $action instanceof InlineAction)
            ->values();
    }

    /**
     * Get the bulk actions.
     *
     * @return Collection<int,\Honed\Table\Actions\BulkAction>
     */
    public function getBulkActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (BaseAction $action): bool => $action instanceof BulkAction)
            ->values();
    }

    /**
     * Get the page actions.
     *
     * @return Collection<int,\Honed\Table\Actions\PageAction>
     */
    public function getPageActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (BaseAction $action): bool => $action instanceof PageAction && $action->isAuthorized())
            ->values();
    }
}
