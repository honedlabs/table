<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

use Honed\Table\Url\Url;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait Urlable
{
    /**
     * @var \Honed\Table\Url\Url|null
     */
    protected $url = null;

    /**
     * Set the properties of the url
     * 
     * @param \Honed\Table\Url\Url|(\Closure(\Honed\Table\Url\Url):void)|array<string,mixed>|string|\Closure(mixed...):string $url
     * @return $this
     */
    public function url(mixed $url): static
    {
        $urlInstance = $this->makeUrl();

        match (true) {
            $url instanceof Url => $this->setUrl($url),
            is_array($url) => $this->getUrl()->assign($url),
            is_callable($url) => $this->evaluate($url, [
                'url' => $urlInstance,
                'link' => $urlInstance,
                'route' => $urlInstance,
            ], [
                Url::class => $urlInstance,
            ]),
            is_string($url) && str($url)->startsWith('/') => $this->getUrl()->url($url),
            default => $this->getUrl()->to($url),
        };

        return $this;
    }

    /**
     * Set the url to a named route.
     * 
     * @param string $route
     * @return $this
     */
    public function route(string $route): static
    {
        return $this->url($route);
    }

    /**
     * Set the url to a named route.
     * 
     * @param string $url
     * @return $this
     */
    public function to(string $url): static
    {
        return $this->url($url);
    }

    /**
     * Create a new url instance if one is not already set.
     * 
     * @internal
     * @return \Honed\Table\Url\Url
     */
    public function makeUrl(): Url
    {
        return $this->url ??= Url::make();
    }

    /**
     * Override the url instance.
     * 
     * @param \Honed\Table\Url\Url|null $url
     */
    public function setUrl(Url|null $url)
    {
        if (\is_null($url)) {
            return;
        }

        $this->url = $url;
    }

    /**
     * Get the url instance.
     *
     * @return \Honed\Table\Url\Url|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Determine if the class has a url associated with it.
     *
     * @return bool
     */
    public function isUrlable()
    {
        return ! \is_null($this->url);
    }

    /**
     * Determine if the class does not have a url associated with it.
     *
     * @return bool
     */
    public function isNotUrlable()
    {
        return ! $this->isUrlable();
    }
}
