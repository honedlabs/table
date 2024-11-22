<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasFormat
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $format = null;

    /**
     * Set the format, chainable.
     *
     * @param  string|\Closure():string  $format
     * @return $this
     */
    public function format(string|\Closure $format): static
    {
        $this->setFormat($format);

        return $this;
    }

    /**
     * Set the format quietly.
     *
     * @param  string|(\Closure():string)|null  $format
     */
    public function setFormat(string|\Closure|null $format): void
    {
        if (is_null($format)) {
            return;
        }
        $this->format = $format;
    }

    /**
     * Get the format.
     */
    public function getFormat(): ?string
    {
        return $this->evaluate($this->format);
    }

    /**
     * Determine if the class does not have a format.
     */
    public function missingFormat(): bool
    {
        return \is_null($this->format);
    }

    /**
     * Determine if the class has a format.
     */
    public function hasFormat(): bool
    {
        return ! $this->missingFormat();
    }
}
