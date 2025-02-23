<?php

declare(strict_types=1);

namespace Honed\Table\Concerns\Support;

trait HasPagination
{
    use HasPages;
    use PagesKey;
    use RecordsKey;

    /**
     * The pagination options for the table.
     *
     * An array will provide the user the ability to select how many rows are
     * shown per page.
     *
     * @var int|array<int,int>|null
     */
    protected $pagination;

    /**
     * The default pagination amount for the table if pagination is an array.
     *
     * @var int|null
     */
    protected $default;

    /**
     * Retrieve the pagination options for the table.
     *
     * @return int|array<int,int>
     */
    public function getPagination(): int|array
    {
        if (isset($this->pagination)) {
            return $this->pagination;
        }

        if (\method_exists($this, 'pagination')) {
            return $this->pagination();
        }

        /** @var int|array<int,int> */
        return config('table.pagination.default', 10);
    }

    /**
     * Retrieve the default pagination options for the table.
     */
    public function getDefault(): int
    {
        if (isset($this->default)) {
            return $this->default;
        }

        return type(config('table.pagination.default', 10))->asInt();
    }

    /**
     * Get the number of records to show per page.
     */
    protected function getRecordsPerPage(): int
    {
        $pagination = $this->getPagination();

        if (! \is_array($pagination)) {
            return $pagination;
        }

        $perPage = $this->getRecordsFromRequest();

        $default = $this->getDefault();

        $perPage = \in_array($perPage, $pagination) ? $perPage : $default;
        $this->pages = $this->generatePages($pagination, $perPage);

        return $perPage;
    }

    /**
     * Get the number of records to show per page from the request.
     */
    protected function getRecordsFromRequest(): int
    {
        /**
         * @var \Illuminate\Http\Request
         */
        $request = $this->getRequest();

        return $request->integer(
            $this->getRecordsKey(),
            $this->getDefault(),
        );
    }
}
