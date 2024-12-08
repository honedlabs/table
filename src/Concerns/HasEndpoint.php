<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasEndpoint
{
    public const Endpoint = '/actions';

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
     * Set the endpoint for the table.
     */
    public function setEndpoint(?string $endpoint): void
    {
        if (\is_null($endpoint)) {
            return;
        }

        $this->endpoint = $endpoint;
    }

    /**
     * Get the endpoint to be used for the table.
     */
    public function getEndpoint(): string
    {
        return $this->inspect('endpoint', static::getDefaultEndpoint());
    }

    /**
     * Get the default endpoint.
     */
    public static function getDefaultEndpoint(): string
    {
        return static::$useEndpoint;
    }
}
