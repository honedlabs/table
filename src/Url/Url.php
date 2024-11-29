<?php

declare(strict_types=1);

namespace Honed\Table\Url;

use Closure;
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
     * @param string|(\Closure(mixed...):string)|null $url
     * @param string|(\Closure():string) $method
     * @param bool|(\Closure():bool) $signed
     * @param int|\Carbon\Carbon|\Closure|null $duration
     * @param bool|(\Closure():bool) $named
     * @param bool|(\Closure():bool) $newTab
     * @param bool|(\Closure():bool) $download
     */
    final public function __construct(
        string|\Closure|null $url = null, 
        string|\Closure $method = 'get',
        bool|\Closure $signed = false,
        int|\Carbon\Carbon|\Closure $duration = 0,
        bool|\Closure $named = false,
        bool|\Closure $newTab = false,
        bool|\Closure $download = false,
    ) {
        parent::__construct();
        $this->setUrl($url);
        $this->setMethod($method);
        $this->setDuration($duration);
        $this->setSigned($signed || $this->getDuration() > 0);
        $this->setNamed($named || $this->checkIfNamed($url));
        $this->setNewTab($newTab);
        $this->setDownload($download);
    }

    /**
     * Make a url parameter object.
     * 
     * @param string|(\Closure(mixed...):string) $url
     * @param string|(\Closure():string)|null $method
     * @param bool|(\Closure():bool) $signed
     * @param int|\Carbon\Carbon|\Closure|null $duration
     * @param bool|(\Closure():bool) $named
     * @param bool|(\Closure():bool) $newTab
     * @param bool|(\Closure():bool) $download
     */
    final public static function make(
        string|\Closure $url = null, 
        string|\Closure|null $method = 'get',
        bool|\Closure|null $signed = false,
        int|\Carbon\Carbon|\Closure|null $duration = 0,
        bool|\Closure|null $named = false,
        bool|\Closure|null $newTab = false,
        bool|\Closure|null $download = false,
    ): static {
        return resolve(static::class, compact(
            'url', 'method', 'signed', 'duration', 'named', 'newTab', 'download'
        ));
    }

    /**
     * Alias for setting a url.
     * 
     * @param string|(\Closure(mixed...):string) $route
     * @return $this
     */
    public function to($route): static
    {
        $this->setNamed($this->checkIfNamed($route));
        $this->setUrl($route);

        return $this;
    }

    /**
     * Set the signed route, chainable.
     * 
     * @param string|(\Closure(mixed...):string) $route
     * @param int|\Carbon\Carbon $duration
     * @return $this
     */
    public function signedRoute($route, int|\Carbon\Carbon $duration = 0): static
    {
        $this->setNamed($this->checkIfNamed($route));
        $this->setUrl($route);
        $this->setSigned(true);
        $this->setDuration($duration);

        return $this;
    }

    /**
     * Resolve and retrieve the url.
     * 
     * @param array $parameters
     * @param array<string,mixed> $typed
     */
    public function getResolvedUrl(array $parameters = [], array $typed = []): string|null
    {
        if ($this->missingUrl()) {
            return null;
        }

        return $this->resolvedUrl ??= $this->resolveUrl($parameters, $typed);
    }

    /**
     * Resolve the url using parameters
     * 
     * @param array $parameters
     * @param array<string,mixed> $typed
     */
    public function resolveUrl(array $parameters = [], array $typed = []): string
    {
        $this->resolvedUrl = match (true) {
            $this->isNotNamed() => $this->getUrl($parameters, $typed),
            $this->isSigned() && $this->isTemporary() => URLFacade::temporarySignedRoute($this->getUrl(), $this->getDuration(), ...$parameters),
            $this->isSigned() => URLFacade::signedRoute($this->getUrl(), ...$parameters),
            default => URLFacade::route($this->getUrl(), ...$parameters),
        };

        return $this->resolvedUrl;
    }

    /**
     * Check if the provided url is a named route. It does not check if the route exists.
     * 
     * @internal
     * @param string|\Closure|null $url
     * @return bool
     */
    protected function checkIfNamed(string|\Closure|null $url): bool
    {
        // Indeterminate
        if (\is_null($url) || !\is_string($url)) {
            return false;
        }
        
        return !str($url)->startsWith('/') && !str($url)->startsWith('http');
    }

    public function toArray()
    {
        return [
            'url' => $this->getResolvedUrl(),
            'method' => $this->getMethod(),
        ];
    }
}

