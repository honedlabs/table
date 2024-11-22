<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

trait HasFallback
{
    protected mixed $fallback = null;

    public function fallback(mixed $fallback): static
    {
        $this->setFallback($fallback);

        return $this;
    }

    public function setFallback(mixed $fallback): void
    {
        $this->fallback = $fallback;
    }

    public function hasFallback(): bool
    {
        return ! $this->missingFallback();
    }

    public function missingFallback(): bool
    {
        return is_null($this->getFallback());
    }

    public function getFallback(): mixed
    {
        return $this->evaluate($this->fallback);
    }
}
