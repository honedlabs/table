<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait PagesKey
{
    /**
     * The query parameter for the page number.
     *
     * @var string|null
     */
    protected $pagesKey;

    /**
     * Set the query parameter for the page number.
     *
     * @return $this
     */
    public function pagesKey(string $pagesKey): static
    {
        $this->pagesKey = $pagesKey;

        return $this;
    }

    /**
     * Get the query parameter for the page number.
     */
    public function getPagesKey(): string
    {
        if (isset($this->pagesKey)) {
            return $this->pagesKey;
        }

        return type(config('table.config.pages', 'page'))->asString();
    }
}
