<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait HasUrl
{
    /**
     * @var string|(\Closure(mixed...):string)|null
     */
    protected $url = null;

    /**
     * Set the url, chainable.
     *
     * @param  string|\Closure(mixed...):string  $url
     * @return $this
     */
    public function url(string|\Closure $url): static
    {
        $this->setUrl($url);

        return $this;
    }

    /**
     * Set the url quietly.
     *
     * @param  string|(\Closure(mixed...):string)|null  $url
     */
    public function setUrl(string|\Closure|null $url): void
    {
        if (is_null($url)) {
            return;
        }
        $this->url = $url;
    }

    /**
     * Get the url using the given closure dependencies.
     *
     * @param  array<string, mixed>  $named
     * @param  array<string, mixed>  $typed
     */
    public function getUrl(array $named = [], array $typed = []): ?string
    {
        return $this->evaluate($this->url, $named, $typed);
    }

    /**
     * Determine if the class has a url.
     */
    public function hasUrl(): bool
    {
        return ! \is_null($this->url);
    }
}
