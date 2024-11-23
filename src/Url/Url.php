<?php

declare(strict_types=1);

namespace Honed\Table\Url;

use Honed\Core\Primitive;
use Illuminate\Support\Facades\URL as UrlFacade;

class Url extends Primitive
{
    use Concerns\HasUrl;
    use Concerns\IsNamed;
    use Concerns\IsNewTab;
    use Concerns\IsSigned;
    use Concerns\HasMethod;
    use Concerns\IsDownload;
    use Concerns\HasDuration;

    /**
     * @var string|null
     */
    protected $resolvedUrl = null;

    /**
     * Create a new parameterised url instance.
     * 
     * @param string|(\Closure():string) $url
     * @param string|(\Closure():string) $method
     * @param bool|(\Closure():bool) $signed
     * @param int|(\Closure():int) $duration
     * @param bool|(\Closure():bool) $named
     * @param bool|(\Closure():bool) $newTab
     * @param bool|(\Closure():bool) $download
     */
    final public function __construct(
        string|\Closure|null $url = null, 
        string|\Closure|null $method = 'get',
        bool|\Closure|null $signed = false,
        int|\Closure|null $duration = 0,
        bool|\Closure|null $named = false,
        bool|\Closure|null $newTab = false,
        bool|\Closure|null $download = false,
    ) {
        parent::__construct();
        $this->setUrl($url);
        $this->setMethod($method);
        $this->setSigned($signed || $duration > 0);
        $this->setDuration($duration);
        $this->setNamed($named || !str($url ?? '')->startsWith('/'));
        $this->setNewTab($newTab);
        $this->setDownload($download);
    }

    /**
     * Make a url parameter object.
     * 
     * @param string|(\Closure():string) $url
     * @param string|(\Closure():string)|null $method
     * @param bool|(\Closure():bool) $signed
     * @param int|(\Closure():int) $duration
     * @param bool|(\Closure():bool) $named
     * @param bool|(\Closure():bool) $newTab
     * @param bool|(\Closure():bool) $download
     */
    final public static function make(
        string|\Closure $url = null, 
        string|\Closure|null $method = 'get',
        bool|\Closure|null $signed = false,
        int|\Closure|null $duration = 0,
        bool|\Closure|null $named = false,
        bool|\Closure|null $newTab = false,
        bool|\Closure|null $download = false,
    ): static {
        return resolve(static::class, compact('url', 'method', 'signed', 'duration', 'named', 'newTab', 'download'));
    }

    /**
     * Set an URL route, chainable.
     * 
     * @param string|(\Closure(...):string)|null $url
     * @return $this
     */
    public function to($url): static
    {
        $this->setNamed(false);
        $this->setUrl($url);

        return $this;
    }

    /**
     * Set a named route, chainable.
     * 
     * @param string|(\Closure(...):string) $route
     * @return $this
     */
    public function route($route): static
    {
        $this->setNamed(true);
        $this->setUrl($route);

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
        $this->setNamed(true);
        $this->setUrl($route);
        $this->setSigned(true);
        $this->setDuration($duration);

        return $this;
    }

    /**
     * Determine if the action does not have an associated URL
     */
    public function isNotUrlable(): bool
    {
        return \is_null($this->getUrl());
    }

    /**
     * Determine if the action has an associated URL
     */
    public function isUrlable(): bool
    {
        return ! $this->isNotUrlable();
    }

    /**
     * Resolve and retrieve the url.
     * 
     * @param array<string, mixed> $parameters
     */
    public function getResolvedUrl(array $parameters = []): string|null
    {
        if ($this->isNotUrlable()) {
            return null;
        }

        return $this->resolvedUrl ??= $this->resolveUrl($parameters);
    }

    /**
     * Resolve the url using parameters
     * 
     * @param array<string, mixed> $parameters
     */
    public function resolveUrl(array $parameters = []): string
    {
        $this->resolvedUrl = match (true) {
            $this->isNotNamed() => $this->evaluate($this->getUrl(), named: $parameters),
            $this->isSigned() && $this->isTemporary() => URLFacade::temporarySignedRoute($this->getUrl(), $this->getDuration(), ...$parameters),
            $this->isSigned() => URLFacade::signedRoute($this->getUrl(), ...$parameters),
            default => URLFacade::route($this->getUrl(), ...$parameters),
        };

        return $this->resolvedUrl;
    }

    public function toArray()
    {
        return [
            'url' => $this->getResolvedUrl(),
            'method' => $this->getMethod(),
        ];
    }
}

