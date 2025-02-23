<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

trait HasEndpoint
{
    /**
     * The endpoint to be used to handle table actions.
     *
     * @var string|null
     */
    protected $endpoint;

    /**
     * Get the endpoint to be used for table actions.
     */
    public function getEndpoint(): string
    {
        if (isset($this->endpoint)) {
            return $this->endpoint;
        }

        if (\method_exists($this, 'endpoint')) {
            return $this->endpoint();
        }

        return type(config('table.endpoint', '/actions'))->asString();
    }
}
