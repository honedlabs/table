<?php

namespace Conquest\Table\Concerns;

/**
 * @mixin \Conquest\Table\Concerns\HasSearch
 * @mixin \Conquest\Table\Concerns\HasSearchAs
 */
trait CanSearch
{
    /**
     * @var bool
     */
    protected $scout;

    /**
     * Set whether to use Laravel Scout for searching.
     *
     * @param  bool|null  $searchAs
     */
    protected function setScout($scout): void
    {
        if (is_null($scout)) {
            return;
        }
        $this->scout = $scout;
    }

    /**
     * Get whether to use Laravel Scout for searching.
     *
     * @return bool
     */
    protected function getScout()
    {
        if (isset($this->scout)) {
            return $this->scout;
        }

        if (method_exists($this, 'scout')) {
            return $this->scout();
        }

        return config('table.search.scout', false);
    }

    /**
     * Determine whether to apply searching.
     */
    public function searching(): bool
    {
        return filled($this->getSearch()) && (filled($this->getSearchFromRequest()) || $this->getScout());
    }

    /**
     * Apply the search to the builder.
     *
     * @param  \Illuminate\Database\Query\Builder  $builder
     */
    protected function applySearch($builder)
    {
        if (! $this->searching()) {
            return;
        }

        if ($this->getScout()) {
            // @phpstan-ignore-next-line
            $builder->search($this->getSearchFromRequest());
        } else {
            $builder->whereAny(
                $this->getSearch(),
                'LIKE',
                "%{$this->getSearchFromRequest()}%"
            );
        }
    }
}
