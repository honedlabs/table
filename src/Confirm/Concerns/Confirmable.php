<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

use Honed\Table\Confirm\Confirm;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait Confirmable
{
    /**
     * @var \Honed\Table\Confirm\Confirm|null
     */
    protected $confirm = null;

    /**
     * Set the properties of the confirm
     * 
     * @param string|\Honed\Table\Confirm\Confirm|(\Closure(\Honed\Table\Confirm\Confirm):void)|array<string,mixed> $confirm
     * @return $this
     */
    public function confirm(mixed $confirm): static
    {
        $confirmInstance = $this->makeConfirm();

        match (true) {
            $confirm instanceof Confirm => $this->setConfirm($confirm),
            \is_array($confirm) => $this->getConfirm()->assign($confirm),
            \is_callable($confirm) => $this->evaluate($confirm, [
                'confirm' => $confirmInstance,
            ], [
                Confirm::class => $confirmInstance,
            ]),
            default => $this->getConfirm()->setDescription($confirm), // string case
        };

        return $this;
    }

    /**
     * Create a new confirm instance if one is not already set.
     * 
     * @internal
     * @return \Honed\Table\Confirm\Confirm
     */
    public function makeConfirm(): Confirm
    {
        return $this->confirm ??= Confirm::make();
    }


    /**
     * Override the confirm instance.
     *
     * @internal
     * @param \Honed\Table\Confirm\Confirm|bool|null $confirm
     */
    public function setConfirm($confirm)
    {
        if (\is_null($confirm)) {
            return;
        }

        $this->confirm = $confirm;
    }

    /**
     * Get the confirm instance.
     *
     * @return \Honed\Table\Confirm\Confirm|null
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    
    /**
     * Determine if the action is not confirmable.
     *
     * @return bool
     */
    public function isNotConfirmable()
    {
        // @phpstan-ignore-next-line
        return \is_null($this->confirm);
    }

    /**
     * Determine if the action is confirmable.
     *
     * @return bool
     */
    public function isConfirmable()
    {
        return ! $this->isNotConfirmable();
    }
}
