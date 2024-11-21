<?php

namespace Honed\Table\Concerns;

use Honed\Table\Actions\BaseAction;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Actions\PageAction;
use Illuminate\Support\Collection;

trait HasActions
{
    /**
     * @var Collection<int, Honed\Table\Actions\BaseAction>
     */
    private Collection $cachedActions;

    /**
     * @var array<int, Honed\Table\Actions\BaseAction>
     */
    protected $actions;

    /**
     * Set the actions for the table.
     *
     * @param  array<int, Honed\Table\Actions\BaseAction>  $actions
     */
    public function setActions(?array $actions): void
    {
        if (is_null($actions)) {
            return;
        }

        $this->actions = $actions;
    }

    /**
     * Get the actions for the table.
     *
     * @internal
     * @return array<int, Honed\Table\Actions\BaseAction>
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
     * Get all available actions.
     *
     * @return Collection<int, Honed\Table\Actions\BaseAction>
     */
    public function getActions(): Collection
    {
        return $this->cachedActions ??= collect($this->definedActions())
            ->filter(static fn (BaseAction $action): bool => $action->isAuthorized());
    }

    /**
     * Get the inline actions.
     *
     * @return Collection<int, Honed\Table\Actions\InlineAction>
     */
    public function getInlineActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (BaseAction $action): bool => $action instanceof InlineAction);
    }

    /**
     * Get the bulk actions.
     *
     * @return Collection<int, Honed\Table\Actions\BulkAction>
     */
    public function getBulkActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (BaseAction $action): bool => $action instanceof BulkAction);
    }

    /**
     * Get the page actions.
     *
     * @return Collection<int, Honed\Table\Actions\PageAction>
     */
    public function getPageActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (BaseAction $action): bool => $action instanceof PageAction);
    }

    /**
     * Get the default inline action for a record.
     *
     * @return ?Honed\Table\Actions\BaseAction
     */
    public function getDefaultAction(): ?BaseAction
    {
        return $this->getInlineActions()
            ->first(fn (InlineAction $action): bool => $action->isDefault());
    }
}
