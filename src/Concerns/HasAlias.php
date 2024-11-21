<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasAlias
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $alias = null;

    /**
     * Set the alias, chainable.
     *
     * @param  string|\Closure():string  $alias
     * @return $this
     */
    public function alias(string|\Closure $alias): static
    {
        $this->setAlias($alias);

        return $this;
    }

    /**
     * Set the alias quietly.
     *
     * @param  string|(\Closure():string)|null  $alias
     */
    public function setAlias(string|\Closure|null $alias): void
    {
        if (is_null($alias)) {
            return;
        }
        $this->alias = $alias;
    }

    /**
     * Get the alias.
     */
    public function getAlias(): ?string
    {
        return $this->evaluate($this->alias);
    }

    /**
     * Determine if the class does not have a alias.
     */
    public function missingAlias(): bool
    {
        return \is_null($this->alias);
    }

    /**
     * Determine if the class has a alias.
     */
    public function hasAlias(): bool
    {
        return ! $this->missingAlias();
    }
}
