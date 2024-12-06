<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait Actionable
{
    /**
     * @var \Closure|null
     */
    protected $action = null;

    /**
     * Set the action to apply.
     * 
     * @param \Closure|string $action
     * @return $this
     */
    public function action($action)
    {
        $this->setAction($action);

        return $this;
    }

    /**
     * Determine whether there is not an action to apply.
     * 
     * @return bool
     */
    public function missingAction(): bool
    {
        return \is_null($this->action);
    }

    /**
     * Determine whether there is an action to apply.
     * 
     * @return bool
     */
    public function hasAction(): bool
    {
        return ! $this->missingAction();
    }

    /**
     * Set the action to apply.
     * 
     * @param \Closure|string|null $action
     * @return void
     */
    public function setAction($action): void
    {
        if (is_null($action)) {
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
     * @return \Closure|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Apply the action to the record.
     * 
     * @param \Illuminate\Database\Eloquent\Model $record
     * @return void
     */
    public function applyAction($record, $modelClass): void
    {
        $this->evaluate(
            value: $this->getAction(),
            named: [
                'record' => $record,
                'model' => $record,
                // user => $record
            ],
            typed: [
                Model::class => $record,
                $modelClass => $record,
            ],
        );
    }
}