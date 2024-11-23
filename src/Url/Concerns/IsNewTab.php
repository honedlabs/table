<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsNewTab
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $newTab = false;

    /**
     * Set the url to open in a new tab, chainable.
     *
     * @param  bool|(\Closure():bool)  $newTab
     * @return $this
     */
    public function newTab(bool|\Closure $newTab): static
    {
        $this->setNewTab($newTab);

        return $this;
    }

    /**
     * Set the url to open in a new tab property quietly.
     *
     * @param  bool|(\Closure():bool)|null  $newTab
     */
    public function setNewTab(bool|\Closure|null $newTab): void
    {
        if (\is_null($newTab)) {
            return;
        }
        $this->newTab = $newTab;
    }

    /**
     * Determine if the url should be opened in a new tab.
     */
    public function isNewTab(): bool
    {
        return (bool) $this->evaluate($this->newTab);
    }

    /**
     * Determine if the url should not be opened in a new tab.
     */
    public function isNotNewTab(): bool
    {
        return ! $this->isNewTab();
    }
}
