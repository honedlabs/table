<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasSubmit
{
    /**
     * @var string|(\Closure():string)|null
     */
    protected $submit = null;

    /**
     * Set the submit, chainable.
     *
     * @param  string|\Closure():string  $submit
     * @return $this
     */
    public function submit(string|\Closure $submit): static
    {
        $this->setSubmit($submit);

        return $this;
    }

    /**
     * Set the submit quietly.
     *
     * @param  string|(\Closure():string)|null  $submit
     */
    public function setSubmit(string|\Closure|null $submit): void
    {
        if (is_null($submit)) {
            return;
        }
        $this->submit = $submit;
    }

    /**
     * Get the submit.
     */
    public function getSubmit(): ?string
    {
        return $this->evaluate($this->submit);
    }

    /**
     * Determine if the class does not have a submit.
     */
    public function missingSubmit(): bool
    {
        return \is_null($this->submit);
    }

    /**
     * Determine if the class has a submit.
     */
    public function hasSubmit(): bool
    {
        return ! $this->missingSubmit();
    }
}
