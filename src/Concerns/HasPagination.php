<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasPagination
{
    const PerPage = 10;

    /**
     * @var int|array<int,int>|null
     */
    protected $pagination;

    /**
     * @var int
     */
    protected $defaultPagination = self::PerPage;

    /**
     * @var int|array<int,int>
     */
    protected static $perPage = self::PerPage;

    /**
     * @var int
     */
    protected static $defaultPerPage = self::PerPage;

    /**
     * Retrieve the pagination options for the table.
     *
     * @return int|array<int,int>
     */
    public function getPagination(): int|array
    {
        if (\property_exists($this, 'pagination') && ! \is_null($this->pagination)) {
            return $this->pagination;
        }

        if (\method_exists($this, 'pagination')) {
            return $this->pagination();
        }

        return static::$perPage;
    }

    /**
     * Retrieve the default pagination options for the table.
     */
    public function getDefaultPagination(): int
    {
        if (\property_exists($this, 'defaultPagination') && ! \is_null($this->defaultPagination)) {
            return $this->defaultPagination;
        }

        if (\method_exists($this, 'defaultPagination')) {
            return $this->defaultPagination();
        }

        return static::$defaultPerPage;
    }

    /**
     * Set the per page amount for all tables.
     *
     * @param  int|array<int,int>  $perPage
     */
    public static function perPage(int|array $perPage): void
    {
        static::$perPage = $perPage;
    }

    /**
     * Set the default per page amount for all tables.
     */
    public static function defaultPerPage(int $perPage): void
    {
        static::$defaultPerPage = $perPage;
    }
}
