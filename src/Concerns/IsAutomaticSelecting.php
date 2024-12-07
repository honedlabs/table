<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait IsAutomaticSelecting
{
    /**
     * @var bool
     */
    protected $select;

    /**
     * @var bool
     */
    protected static $automaticSelect = false;

    /**
     * Set the automatic select flag.
     */
    public static function automaticallySelect(bool $select = true): void
    {
        static::$automaticSelect = $select;
    }

    /**
     * Determine if the url should be downloaded.
     */
    public function isAutomaticSelecting(): bool
    {
        return $this->inspect('select', static::$automaticSelect);
    }

    /**
     * Determine if the url should not be downloaded.
     */
    public function isNotAutomaticSelecting(): bool
    {
        return ! $this->isAutomaticSelecting();
    }
}
