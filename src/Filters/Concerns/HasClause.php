<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

use Honed\Table\Filters\Enums\Clause;

trait HasClause
{
    /**
     * @var \Honed\Table\Filters\Enums\Clause|null
     */
    protected $clause = null;

    /**
     * Set the clause, chainable.
     *
     * @return $this
     *
     * @throws \ValueError
     */
    public function clause(string|Clause $clause): static
    {
        $this->setClause($clause);

        return $this;
    }

    /**
     * Set the clause quietly.
     *
     * @throws \ValueError
     */
    public function setClause(string|Clause|null $clause): void
    {
        if (\is_null($clause)) {
            return;
        }

        $this->clause = $clause instanceof Clause ? $clause : Clause::from($clause);
    }

    /**
     * Determine if the class has a clause.
     */
    public function missingClause(): bool
    {
        return \is_null($this->clause);
    }

    /**
     * Determine if the class has a clause.
     */
    public function hasClause(): bool
    {
        return ! $this->missingClause();
    }

    /**
     * Get the clause.
     */
    public function getClause(): ?Clause
    {
        return $this->clause;
    }

    /**
     * Set the clause to be `where`.
     *
     * @return $this
     */
    public function is(): static
    {
        return $this->clause(Clause::Is);
    }

    /**
     * Set the clause to be `whereNot`.
     *
     * @return $this
     */
    public function isNot(): static
    {
        return $this->clause(Clause::IsNot);
    }

    /**
     * Set the clause to be `where`, with a `%` prefix.
     *
     * @return $this
     */
    public function startsWith(): static
    {
        return $this->clause(Clause::StartsWith);
    }

    /**
     * Alias for `startsWith`.
     *
     * @return $this
     */
    public function beginsWith(): static
    {
        return $this->startsWith();
    }

    /**
     * Set the clause to be `where`, with a `%` suffix.
     *
     * @return $this
     */
    public function endsWith(): static
    {
        return $this->clause(Clause::EndsWith);
    }

    /**
     * Set the clause to be `whereIn`.
     *
     * @return $this
     */
    public function contains(): static
    {
        return $this->clause(Clause::Contains);
    }

    /**
     * Set the clause to be `whereNotIn`.
     *
     * @return $this
     */
    public function doesNotContain(): static
    {
        return $this->clause(Clause::DoesNotContain);
    }

    /**
     * Set the clause to be `whereJsonContains`.
     *
     * @return $this
     */
    public function json(): static
    {
        return $this->clause(Clause::Json);
    }

    /**
     * Set the clause to be `whereJsonDoesntContain`.
     *
     * @return $this
     */
    public function notJson(): static
    {
        return $this->clause(Clause::NotJson);
    }

    /**
     * Set the clause to be `whereJsonLength`.
     *
     * @return $this
     */
    public function jsonLength(): static
    {
        return $this->clause(Clause::JsonLength);
    }

    /**
     * Set the clause to be `whereFullText`.
     *
     * @return $this
     */
    public function fullText(): static
    {
        return $this->clause(Clause::FullText);
    }

    /**
     * Set the clause to be `whereJsonContainsKey`.
     *
     * @return $this
     */
    public function jsonKey(): static
    {
        return $this->clause(Clause::JsonKey);
    }

    /**
     * Set the clause to be `whereJsonDoesntContainKey`.
     *
     * @return $this
     */
    public function notJsonKey(): static
    {
        return $this->clause(Clause::JsonNotKey);
    }

    /**
     * Set the clause to be `whereJsonOverlaps`.
     *
     * @return $this
     */
    public function jsonOverlap(): static
    {
        return $this->clause(Clause::JsonOverlaps);
    }

    /**
     * Alias for `jsonOverlap`.
     *
     * @return $this
     */
    public function jsonOverlaps(): static
    {
        return $this->jsonOverlap();
    }

    /**
     * Set the clause to be `whereJsonDoesntOverlap`.
     *
     * @return $this
     */
    public function jsonDoesNotOverlap(): static
    {
        return $this->clause(Clause::JsonDoesNotOverlap);
    }

    /**
     * Set the clause to be `where`, with a like operator.
     *
     * @return $this
     */
    public function like(): static
    {
        return $this->clause(Clause::Like);
    }
}
