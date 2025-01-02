<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

use Honed\Table\Filters\Enums\DateClause;

trait HasDateClause
{
    /**
     * @var \Honed\Table\Filters\Enums\DateClause|null
     */
    protected $clause = null;

    /**
     * Set the clause, chainable.
     *
     *
     * @return $this
     *
     * @throws \ValueError
     */
    public function clause(string|DateClause $clause): static
    {
        $this->setClause($clause);

        return $this;
    }

    /**
     * Set the clause quietly.
     *
     *
     * @throws \ValueError
     */
    public function setClause(string|DateClause|null $clause): void
    {
        if (\is_null($clause)) {
            return;
        }

        $this->clause = $clause instanceof DateClause ? $clause : DateClause::from($clause);
    }

    /**
     * Determine if the class has a date clause.
     */
    public function hasClause(): bool
    {
        return ! \is_null($this->clause);
    }

    /**
     * Get the date clause.
     */
    public function getClause(): ?DateClause
    {
        return $this->clause;
    }

    /**
     * Set the clause to use the entire date.
     *
     * @return $this
     */
    public function date(): static
    {
        return $this->clause(DateClause::Date);
    }

    /**
     * Set the clause to use the day.
     *
     * @return $this
     */
    public function day(): static
    {
        return $this->clause(DateClause::Day);
    }

    /**
     * Set the clause to use the month.
     *
     * @return $this
     */
    public function month(): static
    {
        return $this->clause(DateClause::Month);
    }

    /**
     * Set the clause to use the year.
     *
     * @return $this
     */
    public function year(): static
    {
        return $this->clause(DateClause::Year);
    }

    /**
     * Set the clause to use the time.
     *
     * @return $this
     */
    public function time(): static
    {
        return $this->clause(DateClause::Time);
    }
}
