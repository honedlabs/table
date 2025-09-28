<?php

declare(strict_types=1);

namespace Honed\Table\Pagination;

use Illuminate\Contracts\Support\Arrayable;

/**
 * @implements Arrayable<string, mixed>
 */
class PaginationData implements Arrayable
{
    /**
     * Whether the pagination data is empty.
     *
     * @var bool
     */
    protected $empty = false;

    public function __construct(bool $empty)
    {
        $this->empty = $empty;
    }

    /**
     * Create a new pagination data instance.
     *
     * @param  \Illuminate\Support\Collection<int, *>  $paginator
     */
    public static function make(mixed $paginator): static
    {
        return new self(
            empty: $paginator->isEmpty()
        );
    }

    /**
     * Set whether the pagination data is empty.
     *
     * @return $this
     */
    public function empty(bool $value = true): static
    {
        $this->empty = $value;

        return $this;
    }

    /**
     * Get whether the pagination data is empty.
     */
    public function isEmpty(): bool
    {
        return $this->empty;
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return $this->representation();
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            'empty' => $this->empty,
        ];
    }
}
