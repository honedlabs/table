<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasPaginator
{
    const Paginator = 'length-aware';

    /**
     * The paginator to use for the table.
     * 
     * @var 'cursor'|'simple'|'length-aware'|'none'|string|null
     */
    protected $paginator;


    /**
     * The default paginator for all tables.
     * 
     * @var 'cursor'|'simple'|'length-aware'|'none'|string
     */
    protected static $defaultPaginator = self::Paginator;


    /**
     * Retrieve the default paginator for the table.
     * 
     * @return 'cursor'|'simple'|'length-aware'|'none'|string
     */
    public function getPaginator(): string
    {
        if (\property_exists($this, 'paginator') && ! \is_null($this->paginator)) {
            return $this->paginator;
        }

        if (\method_exists($this, 'paginator')) {
            return $this->paginator();
        }

        return static::$defaultPaginator;
    }

    /**
     * Set the default paginator for the table.
     * 
     * @param 'cursor'|'simple'|'length-aware'|'none'|string $paginator
     */
    public static function usePaginator(string $paginator): void
    {
        static::$defaultPaginator = $paginator;
    }

}