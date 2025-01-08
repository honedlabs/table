<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

trait IsDeselecting
{
    /**
     * @var bool
     */
    protected $deselect = false;

    /**
     * Set as deselecting, chainable.
     *
     * @return $this
     */
    public function deselect(bool $deselect = true): static
    {
        $this->setDeselecting($deselect);

        return $this;
    }

    /**
     * Set the deselect property quietly.
     */
    public function setDeselecting(bool $deselect): void
    {
        $this->deselect = $deselect;
    }

    /**
     * Determine if is deselecting.
     */
    public function isDeselecting(): bool
    {
        return $this->deselect;
    }
}
