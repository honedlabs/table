<?php

declare(strict_types=1);

namespace Honed\Table\Pagination\Concerns;

use Honed\Table\Pagination\Enums\Paginator;

/**
 * @mixin \Honed\Core\Concerns\Inspectable
 */
trait HasPaginator
{
    /**
     * Set the pagination type to use.
     *
     * @param  Paginator|string|null  $paginator
     * @return void
     */
    public function setPaginator(Paginator|string|null $paginator): void
    {
        if (is_null($paginator)) {
            return;
        }
        $this->paginator = $this->resolvePaginator($paginator);
    }

    /**
     * Retrieve the pagination type to use.
     *
     * @return Paginator
     */
    public function getPaginator(): Paginator
    {
        return $this->resolvePaginator($this->inspect('paginator', Paginator::Default));
    }

    /**
     * Resolve the paginator from the defined value.
     *
     * @param  string|Paginator  $paginator
     * @return Paginator
     */
    protected function resolvePaginator(string|Paginator $paginator): Paginator
    {
        return $paginator instanceof Paginator
            ? $paginator
            : Paginator::from($paginator);
    }
}
