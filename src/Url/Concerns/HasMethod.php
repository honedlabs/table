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
    protected $method = Request::METHOD_GET;

    /**
     * Set the method, chainable.
     *
     * @param  string  $method
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
     * @param  string|null  $method
     * @throws \InvalidArgumentException
     */
    public function setMethod(string|null $method): void
    {
        if (\is_null($method)) {
            return;
        }

        $method = Str::upper($method);

        if (! \in_array($method, [Request::METHOD_GET, Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE])) {
            throw new \InvalidArgumentException("The provided HTTP method [{$method}] is not valid.");
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
    public function asGet(): static
    {
        return $this->method(Request::METHOD_GET);
    }

    /**
     * Use the post method for the route, chainable.
     *
     * @return $this
     */
    public function asPost(): static
    {
        return $this->method(Request::METHOD_POST);
    }

    /**
     * Use the put method for the route, chainable.
     *
     * @return $this
     */
    public function asPut(): static
    {
        return $this->method(Request::METHOD_PUT);
    }

    /**
     * Use the patch method for the route, chainable.
     *
     * @return $this
     */
    public function asPatch(): static
    {
        return $this->method(Request::METHOD_PATCH);
    }

    /**
     * Use the delete method for the route, chainable.
     *
     * @return $this
     */
    public function asDelete(): static
    {
        return $this->method(Request::METHOD_DELETE);
    }
}
