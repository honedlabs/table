<?php

declare(strict_types=1);

namespace Honed\Table\Pagination;

class SimpleData extends CursorData
{
    /**
     * The current page.
     *
     * @var int
     */
    protected $currentPage;

    public function __construct(
        bool $empty,
        ?string $prevLink,
        ?string $nextLink,
        int $perPage,
        int $currentPage
    ) {
        parent::__construct($empty, $prevLink, $nextLink, $perPage);

        $this->currentPage = $currentPage;
    }

    /**
     * Create a new simple data instance.
     *
     * @param \Illuminate\Contracts\Pagination\Paginator<int, *> $paginator
     */
    public static function make(mixed $paginator): static
    {
        return new self(
            empty: $paginator->isEmpty(),
            prevLink: $paginator->previousPageUrl(),
            nextLink: $paginator->nextPageUrl(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage()
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
            'currentPage' => $this->currentPage,
        ];
    }
}
