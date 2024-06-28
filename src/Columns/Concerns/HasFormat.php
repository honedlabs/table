<?php

namespace Conquest\Table\Columns\Concerns;

use Closure;

trait HasFormat
{
    protected string|Closure $format = null;

    public function format(string|Closure $format): static
    {
        $this->setFormat($format);
        return $this;
    }

    public function formatUsing(string|Closure $format): static
    {
        return $this->format($format);
    }

    protected function setFormat(string|Closure $format): void
    {
        $this->format = $format;
    }

    public function hasFormat(): bool
    {
        return !is_null($this->format);
    }

    public function getFormat(): string
    {
        return $this->evaluate($this->format);
    }
}