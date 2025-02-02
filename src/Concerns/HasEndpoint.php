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
     * Set the endpoint for the table.
     */
    public function setEndpoint(string|null $endpoint): void
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
        return match (true) {
            \property_exists($this, 'endpoint') => $this->endpoint,
            \method_exists($this, 'endpoint') => $this->endpoint($this),
            default => config('table.endpoint', '/actions'),
        };
    }
}
