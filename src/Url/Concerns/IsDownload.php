<?php

declare(strict_types=1);

namespace Honed\Table\Url\Concerns;

/**
 * @mixin \Honed\Core\Concerns\Evaluable
 */
trait IsDownload
{
    /**
     * @var bool|(\Closure():bool)
     */
    protected $download = false;

    /**
     * Set the url to be downloaded, chainable.
     *
     * @param  bool|(\Closure():bool)  $download
     * @return $this
     */
    public function download(bool|\Closure $download = true): static
    {
        $this->setDownload($download);

        return $this;
    }

    /**
     * Set the url to be downloaded quietly.
     *
     * @param  bool|(\Closure():bool)|null  $download
     */
    public function setDownload(bool|\Closure|null $download): void
    {
        if (\is_null($download)) {
            return;
        }
        $this->download = $download;
    }

    /**
     * Determine if the url should be downloaded.
     */
    public function isDownload(): bool
    {
        return (bool) $this->evaluate($this->download);
    }

    /**
     * Determine if the url should not be downloaded.
     */
    public function isNotDownload(): bool
    {
        return ! $this->isDownload();
    }
}
