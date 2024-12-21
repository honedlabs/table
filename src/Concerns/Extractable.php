<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait Extractable
{
    /**
     * @var bool
     */
    protected $extract;

    /**
     * @var bool
     */
    protected static $useExtract = false;

    /**
     * Configure all classes to use extraction by default.
     */
    public static function useExtraction(bool $extract = true): void
    {
        static::$useExtract = $extract;
    }

    /**
     * Determine if all classes should use extraction by default.
     */
    public static function usesExtraction(): bool
    {
        return static::$useExtract;
    }

    /**
     * Set the extraction flag.
     */
    public function setExtract(bool $extract = true): void
    {
        $this->extract = $extract;
    }

    /**
     * Determine if the table should use extraction.
     */
    public function isExtractable(): bool
    {
        return $this->inspect('extract', static::usesExtraction());
    }

    /**
     * Determine if the table should not use extraction.
     */
    public function isNotExtractable(): bool
    {
        return ! $this->isExtractable();
    }
}
