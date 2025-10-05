<?php

declare(strict_types=1);

namespace Honed\Table\Pagination;

class CursorData extends PaginationData
{
    /**
     * The url of the previous page.
     *
     * @var string|null
     */
    protected $prevLink;

    /**
     * The url of the next page.
     *
     * @var string|null
     */
    protected $nextLink;

    /**
     * The number of records per page.
     *
     * @var int
     */
    protected $perPage;

    public function __construct(
        bool $empty,
        ?string $prevLink,
        ?string $nextLink,
        int $perPage
    ) {
        parent::__construct($empty);

        $this->prevLink = $prevLink;
        $this->nextLink = $nextLink;
        $this->perPage = $perPage;
    }

    /**
     * Create a new cursor data instance.
     *
     * @param \Illuminate\Contracts\Pagination\CursorPaginator<int, *> $paginator
     */
    public static function make(mixed $paginator): static
    {
        return new self(
            empty: $paginator->isEmpty(),
            prevLink: $paginator->previousPageUrl(),
            nextLink: $paginator->nextPageUrl(),
            perPage: $paginator->perPage()
        );
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            ...parent::representation(),
            'prevLink' => $this->prevLink,
            'nextLink' => $this->nextLink,
            'perPage' => $this->perPage,
        ];
    }
}
