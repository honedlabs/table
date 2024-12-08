<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

use Honed\Table\Filters\Enums\Operator;

trait HasOperator
{
    /**
     * @var \Honed\Table\Filters\Enums\Operator|null
     */
    protected $operator = null;

    /**
     * Set the operator to be used, chainable.
     *
     * @return $this
     */
    public function operator(string|Operator $operator): static
    {
        $this->setOperator($operator);

        return $this;
    }

    /**
     * Set the operator quietly.
     */
    public function setOperator(string|Operator|null $operator): void
    {
        if (is_null($operator)) {
            return;
        }
        $this->operator = $operator instanceof Operator ? $operator : Operator::from($operator);
    }

    /**
     * Get the operator to be used.
     */
    public function getOperator(): ?Operator
    {
        return $this->operator;
    }

    /**
     * Determine if the class has an operator.
     */
    public function missingOperator(): bool
    {
        return \is_null($this->operator);
    }

    /**
     * Determine if the class has an operator.
     */
    public function hasOperator(): bool
    {
        return ! $this->missingOperator();
    }

    /**
     * Set the operator to be '>'.
     *
     * @return $this
     */
    public function gt(): static
    {
        return $this->operator(Operator::GreaterThan);
    }

    /**
     * Set the operator to be '>='.
     *
     * @return $this
     */
    public function gte(): static
    {
        return $this->operator(Operator::GreaterThanOrEqual);
    }

    /**
     * Set the operator to be '<'.
     *
     * @return $this
     */
    public function lt(): static
    {
        return $this->operator(Operator::LessThan);
    }

    /**
     * Set the operator to be '<='.
     *
     * @return $this
     */
    public function lte(): static
    {
        return $this->operator(Operator::LessThanOrEqual);
    }

    /**
     * Set the operator to be '='.
     *
     * @return $this
     */
    public function eq(): static
    {
        return $this->operator(Operator::Equal);
    }

    /**
     * Set the operator to be '!='.
     *
     * @return $this
     */
    public function neq(): static
    {
        return $this->operator(Operator::NotEqual);
    }

    /**
     * Alias for eq().
     *
     * @return $this
     */
    public function equals(): static
    {
        return $this->eq();
    }

    /**
     * Alias for eq().
     *
     * @return $this
     */
    public function equal(): static
    {
        return $this->eq();
    }

    /**
     * Alias for neq().
     *
     * @return $this
     */
    public function notEqual(): static
    {
        return $this->neq();
    }

    /**
     * Alias for gt().
     *
     * @return $this
     */
    public function greaterThan(): static
    {
        return $this->gt();
    }

    /**
     * Alias for gte().
     *
     * @return $this
     */
    public function greaterThanOrEqual(): static
    {
        return $this->gte();
    }

    /**
     * Alias for lt().
     *
     * @return $this
     */
    public function lessThan(): static
    {
        return $this->lt();
    }

    /**
     * Alias for lte().
     *
     * @return $this
     */
    public function lessThanOrEqual(): static
    {
        return $this->lte();
    }

    /**
     * Set the operator to be 'like'.
     *
     * @return $this
     */
    public function search(): static
    {
        return $this->operator(Operator::Like);
    }
}
