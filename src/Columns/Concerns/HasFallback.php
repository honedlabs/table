<?php

declare(strict_types=1);

namespace Honed\Core\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasFallback
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $fallback = null;

    /**
     * @var string
     */
    protected static $defaultFallback = null;

    /**
     * Set the fallback, chainable.
     *
     * @param  string|\Closure():string  $fallback
     * @return $this
     */
    public function fallback(string|\Closure $fallback): static
    {
        $this->setFallback($fallback);

        return $this;
    }

    /**
     * Set the fallback quietly.
     *
     * @param  string|(\Closure():string)|null  $fallback
     */
    public function setFallback(string|\Closure|null $fallback): void
    {
        if (is_null($fallback)) {
            return;
        }
        $this->fallback = $fallback;
    }

    /**
     * Configure the default fallback to use for all missing values.
     * 
     * @param string $fallback
     * @return void
     */
    public static function setDefaultFallback(string $fallback): void
    {
        static::$defaultFallback = $fallback;
    }

    /**
     * Get the fallback.
     */
    public function getFallback(): ?string
    {
        return $this->evaluate($this->fallback) ?? static::$defaultFallback;
    }

    /**
     * Determine if the class does not have a fallback.
     */
    public function missingFallback(): bool
    {
        return \is_null($this->fallback);
    }

    /**
     * Determine if the class has a fallback.
     */
    public function hasFallback(): bool
    {
        return ! $this->missingFallback();
    }
}
