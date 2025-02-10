<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasEndpoint
{
    /**
     * @var string|null
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected static $defaultEndpoint = '/actions';

    /**
     * Get the endpoint to be used for table actions.
     */
    public function getEndpoint(): string
    {
        return match (true) {
            \property_exists($this, 'endpoint') && ! \is_null($this->endpoint) => $this->endpoint,
            \method_exists($this, 'endpoint') => $this->endpoint(),
            default => static::getDefaultEndpoint(),
        };
    }

    public static function useEndpoint(string $endpoint): void
    {
        static::$defaultEndpoint = $endpoint;
    }

    public static function getDefaultEndpoint(): string
    {
        return static::$defaultEndpoint;
    }
}
