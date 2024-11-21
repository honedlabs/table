<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Pagination\Enums\Paginator;
use Honed\Table\Pagination\Pagination;

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
     * @var string|Paginator
     */
    protected $paginator;

    /**
     * @var string
     */
    protected static $defaultPaginator = Paginator::Default;

    /**
     * @var string
     */
    protected $pageAs;

    /**
     * @var string
     */
    protected static $defaultPageAs = 'page';

    /**
     * @var string
     */
    protected $countAs;

    /**
     * @var string
     */
    protected static $defaultCountAs = 'show';

    /**
     * Configure the default number of items to show per page.
     * 
     * @param int $perPage
     * @return void
     */
    public static function setDefaultPerPage(int $defaultPerPage)
    {
        static::$defaultPerPage = $defaultPerPage;
    }

    /**
     * Configure the options for the number of items to show per page.
     * 
     * @param int|array<int,int> $perPage
     * @return void
     */
    public static function setPerPage(int|array $perPage)
    {
        static::$perPage = $perPage;
    }

    /**
     * Configure the default paginator to use.
     * 
     * @param string|Paginator $paginator
     * @return void
     */
    public static function setDefaultPaginator(string|Paginator $paginator)
    {
        static::$defaultPaginator = $paginator;
    }

    /**
     * Configure the query parameter to use for the page number.
     * 
     * @param string $pageAs
     * @return void
     */
    public static function setDefaultPageAs(string $pageAs)
    {
        static::$defaultPageAs = $pageAs;
    }

    /**
     * Configure the query parameter to use for the number of items to show.
     * 
     * @param string $countAs
     * @return void
     */
    public static function setDefaultCountAs(string $countAs)
    {
        static::$defaultCountAs = $countAs;
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
     * @return string|Paginator
     */
    public function getPaginator()
    {
        return $this->inspect('paginator', static::$defaultPaginator);
    }

    /**
     * Get the query parameter to use for the page number.
     * 
     * @return string
     */
    public function getPageAs()
    {
        return $this->inspect('pageAs', static::$defaultPageAs);
    }

    /**
     * Get the query parameter to use for the number of items to show.
     * 
     * @return string
     */
    public function getCountAs()
    {
        return $this->inspect('countAs', static::$defaultCountAs);
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
     * @return array<int,Pagination>
     */
    public function getPaginationCounts(?int $active = null): array
    {
        $perPage = $this->getPerPage();

        return is_array($perPage)
            ? array_map(fn ($count) => Pagination::make($count, $count === $active), $perPage)
            : [Pagination::make($perPage, true)];
    }

    /**
     * Get the number of items to show per page from the request query parameters.
     * 
     * @return int
     */
    public function getPageCount(): int
    {
        $count = $this->getPaginationCounts();
        if (is_int($count)) {
            return $count;
        }
        if (in_array($term = $this->getCountAsTerm(), $count)) {
            return $term;
        }

        return $this->getDefaultPerPage();
    }
}
