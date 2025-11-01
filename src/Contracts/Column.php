<?php

declare(strict_types=1);

namespace Honed\Table\Contracts;

use Honed\Infolist\Contracts\Entryable;
use Honed\Refine\Filters\Filter;
use Honed\Refine\Searches\Search;
use Honed\Refine\Sorts\Sort;
use Illuminate\Contracts\Database\Query\Expression;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

/**
 * @extends \Illuminate\Contracts\Support\Arrayable<string, mixed>
 */
interface Column extends Arrayable, Entryable
{
    public function getName(): string;

    public function getParameter(): string;

    public function getSelects(): array;

    public function isSelectable(): bool;

    public function active(bool $active = true): static;

    public function isAlways(): bool;

    public function isKey(): bool;

    public function qualifyColumn(string|Expression $column, Builder $builder): mixed;

    public function isActive(): bool;

    /**
     * Get the sort instance.
     */
    public function getSort(): ?Sort;

    /**
     * Get the filter instance.
     */
    public function getFilter(): ?Filter;

    /**
     * Get the search instance.
     */
    public function getSearch(): ?Search;
}
