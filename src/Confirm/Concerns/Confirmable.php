<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

use Illuminate\Support\Str;
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
     * @param  string|\Honed\Table\Confirm\Confirm|(\Closure(\Honed\Table\Confirm\Confirm):void|\Honed\Table\Confirm\Confirm)|array<string,mixed>  $confirm
     * @return $this
     */
    public function confirm(mixed $confirm): static
    {
        if (\is_null($confirm)) {
            return $this;
        }

        $instance = $this->confirmInstance();

        match (true) {
            $confirm instanceof Confirm => $this->setConfirm($confirm),
            \is_array($confirm) => $this->getConfirm()->assign($confirm),
            \is_callable($confirm) => $this->evaluate($confirm, [
                'confirm' => $instance,
            ], [
                Confirm::class => $instance,
            ]),
            default => $this->getConfirm()->setTitle($confirm),
        };

        return $this;
    }

    /**
     * Create a new confirm instance if one is not already set.
     */
    public function confirmInstance(): Confirm
    {
        return $this->confirm ??= Confirm::make();
    }

    /**
     * Set the confirm instance quietly.
     */
    public function setConfirm(Confirm|null $confirm)
    {
        if (\is_null($confirm)) {
            return;
        }

        $this->confirm = $confirm;
    }

    /**
     * Get the confirm instance.
     *
     * @param  'title'|'description'|'cancel'|'success'|'intent'|null  $key
     */
    public function getConfirm(?string $key = null): Confirm|string|null
    {
        return \is_null($key) || ! $this->isConfirmable()
            ? $this->confirm
            : $this->confirm->{'get'.Str::studly($key)}();
    }

    /**
     * Determine if the action is confirmable.
     */
    public function isConfirmable(): bool
    {
        return ! \is_null($this->confirm);
    }
}
