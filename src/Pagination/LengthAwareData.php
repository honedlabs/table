<?php

declare(strict_types=1);

namespace Honed\Table\Pagination;

class LengthAwareData extends SimpleData
{
    /**
     * The total number of records.
     *
     * @var int
     */
    protected $total;

    /**
     * The index of the first record.
     *
     * @var int
     */
    protected $from;

    /**
     * The index of the last record.
     *
     * @var int
     */
    protected $to;

    /**
     * The url of the first page.
     *
     * @var string
     */
    protected $firstLink;

    /**
     * The url of the last page.
     *
     * @var string
     */
    protected $lastLink;

    /**
     * The pagination links.
     *
     * @var array<int, array<string, mixed>>
     */
    protected $links;

    /**
     * @param  array<int, array<string, mixed>>  $links
     */
    public function __construct(
        bool $empty,
        ?string $prevLink,
        ?string $nextLink,
        int $perPage,
        int $currentPage,
        int $total,
        ?int $from,
        ?int $to,
        string $firstLink,
        string $lastLink,
        array $links
    ) {
        parent::__construct($empty, $prevLink, $nextLink, $perPage, $currentPage);

        $this->total = $total;
        $this->from = $from ?? 0;
        $this->to = $to ?? 0;
        $this->firstLink = $firstLink;
        $this->lastLink = $lastLink;
        $this->links = $links;
    }

    /**
     * Create a new length aware data instance.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, *> $paginator
     */
    public static function make(mixed $paginator): static
    {
        return new self(
            empty: $paginator->isEmpty(),
            prevLink: $paginator->previousPageUrl(),
            nextLink: $paginator->nextPageUrl(),
            perPage: $paginator->perPage(),
            currentPage: $paginator->currentPage(),
            total: $paginator->total(),
            from: $paginator->firstItem(),
            to: $paginator->lastItem(),
            firstLink: $paginator->url(1),
            lastLink: $paginator->url($paginator->lastPage()),
            links: static::links($paginator),
        );
    }

    /**
     * Create the pagination links.
     *
     * @param \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, *> $paginator
     */
    public static function links(mixed $paginator): array
    {
        $currentPage = $paginator->currentPage();
        $lastPage = $paginator->lastPage();
        $onEachSide = 3;

        $start = max(1, min($currentPage - $onEachSide, $lastPage - ($onEachSide * 2)));
        $end = min($lastPage, max($currentPage + $onEachSide, ($onEachSide * 2 + 1)));

        return array_map(
            static fn (int $page) => [
                'url' => $paginator->url($page),
                'label' => (string) $page,
                'active' => $currentPage === $page,
            ],
            range($start, $end)
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
            'total' => $this->total,
            'from' => $this->from,
            'to' => $this->to,
            'firstLink' => $this->firstLink,
            'lastLink' => $this->lastLink,
            'links' => $this->links,
        ];
    }
}
