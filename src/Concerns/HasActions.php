<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Actions\Contracts\Action;
use Honed\Table\Actions\BulkAction;
use Honed\Table\Actions\InlineAction;
use Honed\Table\Actions\PageAction;
use Illuminate\Support\Collection;

trait HasActions
{
    /**
     * @var array<int,\Honed\Table\Actions\Contracts\Action>
     */
    protected $actions;

    /**
     * Set the actions for the table.
     *
     * @param  array<int,\Honed\Table\Actions\Contracts\Action>|null  $actions
     */
    public function setActions(?array $actions): void
    {
        if (\is_null($actions)) {
            return;
        }

        $this->actions = $actions;
    }

    /**
     * Determine if the table has actions.
     */
    public function hasActions(): bool
    {
        return $this->getActions()->isNotEmpty();
    }

    /**
     * Get the available actions.
     *
     * @return Collection<int,\Honed\Table\Actions\Contracts\Action>
     */
    public function getActions(): Collection
    {
        return collect(match(true) {
            \property_exists($this, 'actions') && !\is_null($this->actions) => $this->actions,
            \method_exists($this, 'actions') => $this->actions(),
            default => [],
        });
    }

    /**
     * Get the inline actions.
     *
     * @return Collection<int,\Honed\Table\Actions\InlineAction>
     */
    public function getInlineActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (Action $action): bool => $action instanceof InlineAction)
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
            ->filter(static fn (Action $action): bool => $action instanceof BulkAction)
            ->values();
    }

    /**
     * Get the page actions.
     * Authorization is applied at this level.
     *
     * @return Collection<int,\Honed\Table\Actions\PageAction>
     */
    public function getPageActions(): Collection
    {
        return $this->getActions()
            ->filter(static fn (Action $action): bool => $action instanceof PageAction && $action->isAuthorized())
            ->values();
    }
}
