<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Request;

trait HasMethod
{
    /**
     * The HTTP method to use for routing.
     *
     * @var string
     */
    protected $method = 'get';

    /**
     * Set the method, chainable.
     *
     * @return $this
     */
    public function method(string $method): static
    {
        $this->setMethod($method);

        return $this;
    }

    /**
     * Set the method quietly.
     *
     * @throws \InvalidArgumentException
     */
    public function setMethod(?string $method): void
    {
        if (\is_null($method)) {
            return;
        }

        $method = Str::lower($method);

        if (! \in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
            throw new \InvalidArgumentException("Invalid HTTP method [{$method}] provided for url.");
        }

        $this->method = $method;
    }

    /**
     * Get the HTTP method.
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * Use the get method for the route, chainable.
     *
     * @return $this
     */
    public function get(): static
    {
        return $this->method(Request::METHOD_GET);
    }

    /**
     * Use the post method for the route, chainable.
     *
     * @return $this
     */
    public function post(): static
    {
        return $this->method(Request::METHOD_POST);
    }

    /**
     * Use the put method for the route, chainable.
     *
     * @return $this
     */
    public function put(): static
    {
        return $this->method(Request::METHOD_PUT);
    }

    /**
     * Use the patch method for the route, chainable.
     *
     * @return $this
     */
    public function patch(): static
    {
        return $this->method(Request::METHOD_PATCH);
    }

    /**
     * Use the delete method for the route, chainable.
     *
     * @return $this
     */
    public function delete(): static
    {
        return $this->method(Request::METHOD_DELETE);
    }
}
