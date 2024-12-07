<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasEndpoint
{
    public const Endpoint = '/table';

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected static $useEndpoint = self::Endpoint;

    /**
     * Set the endpoint to be used for all tables.
     */
    public static function useEndpoint(?string $endpoint = null): void
    {
        static::$useEndpoint = $endpoint ?? self::Endpoint;
    }

    /**
     * Get the endpoint to be used for the table.
     */
    public function getEndpoint(): string
    {
        return $this->inspect('endpoint', static::$useEndpoint);
    }
}
