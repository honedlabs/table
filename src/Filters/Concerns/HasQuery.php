<?php

declare(strict_types=1);

namespace Honed\Table\Filters\Concerns;

use Closure;
use Honed\Table\Exceptions\QueryNotDefined;

trait HasQuery
{
    /**
     * @var (\Closure(mixed...):void)|null
     */
    protected $query = null;

    /**
     * Set the query, chainable.
     * 
     * @param  \Closure(mixed...):void  $query
     * @return $this
     */
    public function query(Closure $query): static
    {
        $this->setQuery($query);

        return $this;
    }

    /**
     * Alias for `query`
     *
     * @param  \Closure(mixed...):void  $query
     * @return $this
     */
    public function using(Closure $query): static
    {
        return $this->query($query);
    }

    /**
     * Set the query quietly
     *
     * @param  \Closure(mixed...):void|null  $query
     */
    public function setQuery(?Closure $query): void
    {
        if (\is_null($query)) {
            return;
        }
        $this->query = $query;
    }

    /**
     * Determine if the class does not have a query.
     */
    public function missingQuery(): bool
    {
        return \is_null($this->query);
    }

    /**
     * Determine if the class has a query.
     */
    public function hasQuery(): bool
    {
        return ! $this->missingQuery();
    }

    /**
     * Get the query.
     * 
     * @return (\Closure(mixed...):void)|null
     */
    public function getQuery(): ?Closure
    {
        return $this->query;
    }
}
