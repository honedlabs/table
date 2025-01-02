<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasIntent
{
    public const Constructive = 'constructive';

    public const Destructive = 'destructive';

    public const Informative = 'informative';

    /**
     * @var string|(\Closure():string)|null
     */
    protected $intent = null;

    /**
     * Set the intent, chainable.
     *
     * @param  string|\Closure():string  $intent
     * @return $this
     */
    public function intent(string|\Closure $intent): static
    {
        $this->setIntent($intent);

        return $this;
    }

    /**
     * Set the intent quietly.
     *
     * @param  string|(\Closure():string)|null  $intent
     */
    public function setIntent(string|\Closure|null $intent): void
    {
        if (is_null($intent)) {
            return;
        }
        $this->intent = $intent;
    }

    /**
     * Get the intent.
     */
    public function getIntent(): ?string
    {
        return $this->evaluate($this->intent);
    }

    /**
     * Determine if the class has a intent.
     */
    public function hasIntent(): bool
    {
        return ! \is_null($this->intent);
    }

    /**
     * Set the intent to constructive.
     *
     * @return $this
     */
    public function constructive(): static
    {
        return $this->intent(self::Constructive);
    }

    /**
     * Set the intent to destructive.
     *
     * @return $this
     */
    public function destructive(): static
    {
        return $this->intent(self::Destructive);
    }

    /**
     * Set the intent to informative.
     *
     * @return $this
     */
    public function informative(): static
    {
        return $this->intent(self::Informative);
    }
}
