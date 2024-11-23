<?php

declare(strict_types=1);

namespace Honed\Table\Actions\Concerns;

use Illuminate\Support\Facades\URL;

trait Routable
{
    /**
     * @var string|null
     */
    protected $route = null;

    /**
     * @var string|null
     */
    protected $resolvedRoute = null;

    /**
     * @var bool
     */
    protected $isNamedRoute = false;

    /**
     * @var bool
     */
    protected $isSignedRoute = false;

    /**
     * @var int
     */
    protected $duration = 0;

    /**
     * @var string
     */
    protected $method = 'get';

    /**
     * @var bool
     */
    protected $newTab = false;

    /**
     * @var bool
     */
    protected $download = false;

    /**
     * Set the URL route, chainable.
     * 
     * @param string|(\Closure(...):string)|null $url
     * @return $this
     */
    public function to($url): static
    {
        $this->setNamedRoute(false);
        $this->setRoute($url);

        return $this;
    }

    /**
     * Set the named route, chainable.
     * 
     * @param string|(\Closure(...):string) $route
     * @return $this
     */
    public function route($route): static
    {
        $this->setNamedRoute(true);
        $this->setRoute($route);

        return $this;
    }

    /**
     * Set the signed route, chainable.
     * 
     * @param string|(\Closure(...):string) $route
     * @param int $duration
     * @return $this
     */
    public function signedRoute($route, int $duration = 0): static
    {
        $this->setNamedRoute(true);
        $this->setRoute($route);
        $this->setSignedRoute(true);
        $this->setDuration($duration);

        return $this;
    }

    /**
     * Set the method to use for the route, chainable.
     * 
     * @param string|null $method
     * @return $this
     */
    public function method(string $method = null): static
    {
        $this->setMethod($method);

        return $this;
    }

    /**
     * Use the get method for the route, chainable.
     * 
     * @return $this
     */
    public function get(): static
    {
        return $this->method('get');
    }

    /**
     * Use the post method for the route, chainable.
     * 
     * @return $this
     */
    public function post(): static
    {
        return $this->method('post');
    }

    /**
     * Use the put method for the route, chainable.
     * 
     * @return $this
     */
    public function put(): static
    {
        return $this->method('put');
    }

    /**
     * Use the patch method for the route, chainable.
     * 
     * @return $this
     */
    public function patch(): static
    {
        return $this->method('patch');
    }

    /**
     * Use the delete method for the route, chainable.
     * 
     * @return $this
     */
    public function delete(): static
    {
        return $this->method('delete');
    }

    /**
     * Set whether the route should open in a new tab, chainable.
     * 
     * @param bool $newTab
     * @return $this
     */
    public function newTab(bool $newTab = true): static
    {
        $this->setNewTab($newTab);

        return $this;
    }

    /**
     * Set whether the route should download the resource, chainable.
     * 
     * @param bool $download
     * @return $this
     */
    public function download(bool $download = true): static
    {
        $this->setDownload($download);

        return $this;
    }

    /**
     * Set the URL route quietly.
     *
     * @param  string|(\Closure(...):string)|null $route
     */
    public function setRoute(string|\Closure|null $route): void
    {
        if (is_null($route)) {
            return;
        }
        $this->route = $route;
    }

    /**
     * Set whether the route is signed quietly.
     */
    public function setSignedRoute(bool $isSignedRoute): void
    {
        $this->isSignedRoute = $isSignedRoute;
    }

    /**
     * Set the duration for the temporary signed route quietly.
     * 
     * @param int|(\Closure():int)|null $duration
     */
    public function setDuration(int|\Closure|null $duration): void
    {
        if (is_null($duration)) {
            return;
        }

        $this->duration = $duration;
    }

    /**
     * Set the method to use for the route quietly.
     * 
     * @param string|null $method
     */
    public function setMethod(?string $method): void
    {
        $method = strtolower($method);

        if (is_null($method)) {
            return;
        }

        if (! in_array($method, ['get', 'post', 'put', 'patch', 'delete'])) {
            throw new \InvalidArgumentException("Invalid HTTP method [{$method}] provided for action route.");
        }

        $this->method = $method;
    }

    /**
     * Set whether the route should open in a new tab quietly.
     */
    public function setNewTab(bool $newTab): void
    {
        $this->newTab = $newTab;
    }

    /**
     * Set whether the route should download the resource quietly.
     */
    public function setDownload(bool $download): void
    {
        $this->download = $download;
    }

    /**
     * Set the named route flag quietly.
     * 
     * @internal
     */
    protected function setNamedRoute(bool $isNamedRoute): void
    {
        $this->isNamedRoute = $isNamedRoute;
    }

    /**
     * Get the route
     */
    public function getRoute(): string|\Closure|null
    {
        return $this->route;
    }

    /**
     * Get the method
     */
    public function getMethod(): string|null
    {
        return $this->method;
    }

    /**
     * Get the duration of the temporary signed route
     */
    public function getDuration(): int
    {
        return value($this->duration);
    }

    /**
     * Determine if the action does not have an associated URL
     */
    public function isNotRoutable(): bool
    {
        return \is_null($this->route);
    }

    /**
     * Determine if the action has an associated URL
     */
    public function isRoutable(): bool
    {
        return ! $this->isNotRoutable();
    }

    /**
     * Determine if the action has a signed route
     */
    public function isSignedRoute(): bool
    {
        return (bool) value($this->isSignedRoute);
    }

    /**
     * Determine if the action is not a signed route
     */
    public function isNotSignedRoute(): bool
    {
        return ! $this->isSignedRoute();
    }

    /**
     * Determine if the action has a temporary signed route
     */
    public function isSignedTemporaryRoute(): bool
    {
        return (bool) ($this->getDuration() > 0 && $this->isSignedRoute());
    }

    /**
     * Determine if the action is not a temporary signed route
     */
    public function isNotSignedTemporaryRoute(): bool
    {
        return ! $this->isSignedTemporaryRoute();
    }

    /**
     * Determine if the action is a named route
     */
    public function isNamedRoute(): bool
    {
        return (bool) value($this->isNamedRoute);
    }

    /**
     * Determine if the action is not a named route
     */
    public function isNotNamedRoute(): bool
    {
        return ! $this->isNamedRoute();
    }

    /**
     * Determine if the action should open in a new tab
     */
    public function isNewTab(): bool
    {
        return (bool) value($this->newTab);
    }

    /**
     * Determine if the action should not open in a new tab
     */
    public function isNotNewTab(): bool
    {
        return ! $this->isNewTab();
    }

    /**
     * Determine if the action should download the resource
     */
    public function isDownload(): bool
    {
        return (bool) value($this->download);
    }

    /**
     * Determine if the action should not download the resource
     */
    public function isNotDownload(): bool
    {
        return ! $this->isDownload();
    }

    /**
     * Resolve and retrieve the route
     * 
     * @param array<string, mixed> $parameters
     */
    public function getResolvedRoute(array $parameters = []): string|null
    {
        if ($this->isNotRoutable()) {
            return null;
        }

        return $this->resolvedRoute ??= $this->resolveRoute($parameters);
    }

    /**
     * Resolve the route
     * 
     * @param array<string, mixed> $parameters
     */
    public function resolveRoute(array $parameters = []): string
    {
        $this->resolvedRoute = match (true) {
            $this->isNotNamedRoute() => $this->evaluate($this->route, named: $parameters),
            $this->isSignedTemporaryRoute() => URL::temporarySignedRoute($this->route, $this->getDuration(), ...$parameters),
            $this->isSignedRoute() => URL::signedRoute($this->route, ...$parameters),
            default => URL::route($this->route, ...$parameters),
        };

        return $this->resolvedRoute;
    }
}
