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
     * @param \Honed\Table\Confirm\Confirm|(\Closure(\Honed\Table\Confirm\Confirm):void)|bool $confirm
     * @return $this
     */
    public function confirm($confirm)
    {
        $this->setConfirm(true);

        if (is_array($confirm)) {
            $this->confirm->assign($confirm);
        }

        if (is_callable($confirm)) {
            $confirm($this->confirm);
            $this->evaluate($confirm, [

            ], [

            ]);
        }

        return $this;
    }

    /**
     * Enable a confirm instance.
     *
     * @internal
     *
     * @param  \Honed\Table\Confirm\Confirm|bool|null  $confirm
     */
    public function setConfirm($confirm)
    {
        if (is_null($confirm)) {
            return;
        }

        $this->confirm ??= match (true) {
            $confirm instanceof Confirm => $confirm,
            (bool) $confirm => Confirm::make(),
            default => null,
        };
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
     * Determine if the action is confirmable.
     *
     * @return bool
     */
    public function isConfirmable()
    {
        return ! \is_null($this->confirm);
    }

    /**
     * Determine if the action is not confirmable.
     *
     * @return bool
     */
    public function isNotConfirmable()
    {
        return ! $this->isConfirmable();
    }
}
