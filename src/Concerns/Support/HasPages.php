<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

use Honed\Table\Page;

trait HasPages
{
    /**
     * The page options of the table if dynamic.
     *
     * @var array<int,\Honed\Table\Page>
     */
    protected $pages = [];

    /**
     * Get the page options of the table.
     *
     * @return array<int,\Honed\Table\Page>
     */
    public function getPages(): array
    {
        return $this->pages;
    }

    /**
     * Generate the pages for the table.
     *
     * @param  array<int,int>  $pagination
     * @return array<int,\Honed\Table\Page>
     */
    public function generatePages(array $pagination, int $active): array
    {
        return \array_map(
            static fn (int $amount) => Page::make($amount, $active),
            $pagination
        );
    }
}
