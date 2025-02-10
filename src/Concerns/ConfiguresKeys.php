<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait ConfiguresKeys
{
    const PageKey = 'page';

    const ColumnKey = 'columns';

    const RecordKey = 'rows';

    /**
     * The key for pagination.
     * 
     * @var string|null
     */

    protected $pageKey;

    /**
     * The default key for pagination.
     * 
     * @var string
     */
    protected static $defaultPageKey = self::PageKey;


    /**
     * The key for the columns to show.
     * 
     * @var string|null
     */
    protected $columnsKey;

    /**
     * The default key for the columns to show.
     * 
     * @var string
     */
    protected static $defaultColumnsKey = self::ColumnKey;

    /**
     * The key for the number of records to show.
     * 
     * @var string|null
     */
    protected $recordsKey;

    /**
     * The default key for the number of records to show.
     * 
     * @var string
     */
    protected static $defaultRecordsKey = self::RecordKey;

    /**
     * Get the key for pagination.


     */
    public function getPageKey(): string

    {
        return match (true) {
            \property_exists($this, 'pageKey') && ! \is_null($this->pageKey) => $this->pageKey,
            \method_exists($this, 'pageKey') => $this->pageKey(),
            default => static::getDefaultPageKey(),
        };
    }

    /**
     * Get the key for the columns to show.
     */
    public function getColumnKey(): string
    {
        return match (true) {

            \property_exists($this, 'columnsKey') && ! \is_null($this->columnsKey) => $this->columnsKey,
            \method_exists($this, 'columnsKey') => $this->columnsKey(),
            default => static::getDefaultColumnsKey(),
        };
    }

    /**
     * Get the key for the number of records to show.
     */
    public function getRecordsKey(): string
    {
        return match (true) {
            \property_exists($this, 'recordsKey') && ! \is_null($this->recordsKey) => $this->recordsKey,
            \method_exists($this, 'recordsKey') => $this->recordsKey(),
            default => static::getDefaultRecordsKey(),
        };
    }

    /**
     * Get the default page key for all tables.
     */
    public static function getDefaultPageKey(): string
    {
        return static::$defaultPageKey;
    }

    /**
     * Get the default columns key for all tables.
     */
    public static function getDefaultColumnsKey(): string
    {
        return static::$defaultColumnsKey;
    }

    /**
     * Get the default records key for all tables.
     */
    public static function getDefaultRecordsKey(): string
    {
        return static::$defaultRecordsKey;
    }

    /**
     * Set the default page key for all tables.

     */
    public static function usePageKey(string $key): void
    {
        static::$defaultPageKey = $key;
    }

    /**
     * Set the default columns key for all tables.
     */
    public static function useColumnsKey(string $key): void
    {
        static::$defaultColumnsKey = $key;
    }

    /**
     * Set the default records key for all tables.
     */
    public static function useRecordsKey(string $key): void
    {
        static::$defaultRecordsKey = $key;
    }

    /**
     * Get the keys for the table as an array.
     * 
     * @return array<string,string>
     */

    public function keysToArray(): array
    {
        return [
            'record' => $this->getKeyName(),
            'records' => $this->getRecordsKey(),
            'sorts' => $this->getSortKey(),
            'search' => $this->getSearchKey(),
            'columns' => $this->getColumnsKey(),
            ...($this->hasMatches() ? ['match' => $this->getMatchKey()] : []),
        ];
    }
}

