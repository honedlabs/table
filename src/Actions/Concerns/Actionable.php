<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

trait Actionable
{
    /**
     * @var \Closure|null
     */
    protected $action = null;

    /**
     * Set the action to apply.
     *
     * @param  \Closure(mixed...):void|string  $action
     * @return $this
     */
    public function action(\Closure|string $action)
    {
        $this->setAction($action);

        return $this;
    }

    /**
     * Determine whether there is an action to apply.
     */
    public function hasAction(): bool
    {
        return ! \is_null($this->action);
    }

    /**
     * Set the action to apply.
     *
     * @param  \Closure(mixed...):void|string|null  $action
     */
    public function setAction(\Closure|string|null $action): void
    {
        if (\is_null($action)) {
            return;
        }

        if (\is_string($action) && class_exists($action) && method_exists($action, '__invoke')) {
            $action = resolve($action)->__invoke(...);
        }

        $this->action = $action;
    }

    /**
     * Get the action to apply.
     *
     * @return \Closure(mixed...):void|null
     */
    public function getAction(): ?\Closure
    {
        return $this->action;
    }
}
