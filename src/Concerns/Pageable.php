<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Enums\Paginator;

trait Pageable
{
    /**
     * @var int
     */
    protected $defaultPerPage;

    /**
     * @var int
     */
    protected static $globalDefaultPerPage = 10;

    /**
     * @var int|array<int,int>
     */
    protected $perPage;

    /**
     * @var int|array<int,int>
     */
    protected static $globalPerPage = 10;

    /**
     * @var string|\Honed\Table\Enums\Paginator
     */
    protected $paginator;

    /**
     * @var string|\Honed\Table\Enums\Paginator
     */
    protected static $globalPaginator = Paginator::Default;

    /**
     * @var string
     */
    protected $pageAs;

    /**
     * @var string
     */
    protected static $globalPageAs = 'page';

    /**
     * @var string
     */
    protected $countAs;

    /**
     * @var string
     */
    protected static $globalCountAs = 'show';

    /**
     * Configure the default number of items to show per page.
     * 
     * @param int $defaultPerPage
     * @return void
     */
    public static function setDefaultPerPage(int $defaultPerPage)
    {
        static::$globalDefaultPerPage = $defaultPerPage;
    }

    /**
     * Configure the options for the number of items to show per page.
     * 
     * @param int|array<int,int> $perPage
     * @return void
     */
    public static function setPerPage(int|array $perPage)
    {
        static::$globalPerPage = $perPage;
    }

    /**
     * Configure the default paginator to use.
     * 
     * @param string|\Honed\Table\Enums\Paginator $paginator
     * @return void
     */
    public static function sePaginator(string|Paginator $paginator)
    {
        static::$globalPaginator = $paginator;
    }

    /**
     * Configure the query parameter to use for the page number.
     * 
     * @param string $pageAs
     * @return void
     */
    public static function setPageAs(string $pageAs)
    {
        static::$globalPageAs = $pageAs;
    }

    /**
     * Configure the query parameter to use for the number of items to show.
     * 
     * @param string $countAs
     * @return void
     */
    public static function setCountAs(string $countAs)
    {
        static::$globalCountAs = $countAs;
    }

    /**
     * Get the default number of items to show per page.
     * 
     * @return int
     */
    public function getDefaultPerPage()
    {
        return $this->inspect('defaultPerPage', static::$globalDefaultPerPage);
    }

    /**
     * Get the options for the number of items to show per page.
     * 
     * @return int|array<int,int>
     */
    public function getPerPage()
    {
        return $this->inspect('perPage', static::$globalPerPage);
    }

    /**
     * Get the default paginator to use.
     * 
     * @return string|\Honed\Table\Enums\Paginator
     */
    public function getPaginator()
    {
        return $this->inspect('paginator', static::$globalPaginator);
    }

    /**
     * Get the query parameter to use for the page number.
     * 
     * @return string
     */
    public function getPageAs()
    {
        return $this->inspect('pageAs', static::$globalPageAs);
    }

    /**
     * Get the query parameter to use for the number of items to show.
     * 
     * @return string
     */
    public function getCountAs()
    {
        return $this->inspect('countAs', static::$globalCountAs);
    }

    /**
     * Get the number of items to show per page from the request query parameters.
     * 
     * @return int|null
     */
    public function getCountAsTerm()
    {
        return request()->input($this->getCountAs(), null);
    }

    /**
     * Get the pagination options for the number of items to show per page.
     * 
     * @param int|null $active
     * @return array<int, array{value: int, active: bool}>
     */
    public function getPaginationCounts(?int $active = null): array
    {
        $perPage = $this->getPerPage();

        return is_array($perPage)
            ? array_map(fn ($count) => ['value' => $count, 'active' => $count === $active], $perPage)
            : [['value' => $perPage, 'active' => true]];
    }

    /**
     * Get the number of items to show per page from the request query parameters.
     * 
     * @return int
     */
    public function getPageCount(): int
    {
        $count = $this->getPerPage();

        if (is_int($count)) {
            return $count;
        }
        if (in_array($term = $this->getCountAsTerm(), $count)) {
            return $term;
        }

        return $this->getDefaultPerPage();
    }
}
