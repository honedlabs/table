<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

use Closure;
use Honed\Table\Actions\Attributes\Confirm;
use Honed\Table\Actions\Confirm\Confirmable;
use ReflectionClass;

trait CanBeConfirmable
{
    /**
     * @var \Honed\Table\Actions\Confirm\Confirm|null
     */
    protected ?Confirmable $confirm = null;

    /**
     * Set the properties of the confirm
     * 
     * @param \Honed\Table\Actions\Confirm\Confirm|bool|null $confirm
     * @return $this
     */
    public function confirm($confirm)
    {
        $this->setConfirm(true);

        if (is_array($confirm)) {
            $this->confirm->setState($confirm);
        }

        if (is_callable($confirm)) {
            $confirm($this->confirm);
        }

        return $this;
    }

    /**
     * Enable a confirm instance.
     *
     * @internal
     *
     * @param  \Honed\Table\Actions\Confirm\Confirm|bool|null  $confirm
     */
    public function setConfirm($confirm)
    {
        if (is_null($confirm)) {
            return;
        }

        $this->confirm ??= match (true) {
            $confirm instanceof Confirmable => $confirm,
            (bool) $confirm => Confirmable::make(),
            default => null,
        };
    }

    /**
     * Get the confirm instance.
     *
     * @return \Honed\Table\Actions\Confirm\Confirm|null
     */
    public function getConfirm()
    {
        if (! $this->isConfirmable()) {

        }

        return $this->confirm;
    }

    /**
     * Evaluate for a possible confirm attribute as a fallback.
     *
     * @internal
     */
    protected function evaluateConfirmAttribute()
    {
        $reflection = new ReflectionClass($this);
        $attributes = $reflection->getAttributes(Confirm::class);

        if (! empty($attributes)) {
            $this->setConfirm($attributes[0]->newInstance());
        }
    }

    /**
     * Check if the action is confirmable.
     *
     * @internal
     * @return bool
     */
    protected function isConfirmable()
    {
        return ! is_null($this->confirm);
    }
}
