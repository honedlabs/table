<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Closure;

trait AdaptsToRefinements
{
    /**
     * The callback to modify the state when refiners are being applied.
     *
     * @var (Closure(mixed...):$this|void)|null
     */
    protected $refining;

    /**
     * The callback to modify the state when filters are being applied.
     *
     * @var (Closure(mixed...):$this|void)|null
     */
    protected $filtering;

    /**
     * The callback to modify the state when searching is being applied.
     *
     * @var (Closure(mixed...):$this|void)|null
     */
    protected $searching;

    /**
     * Register a callback to modify the state when refiners are being applied.
     *
     * @param  (Closure(mixed...):$this|void)  $refining
     * @return $this
     */
    public function whenRefining($refining)
    {
        $this->refining = $refining;

        return $this;
    }

    /**
     * Get the callback to modify the state when refiners are being applied.
     *
     * @return (Closure(mixed...):$this|void)|null
     */
    public function getRefiningCallback()
    {
        return $this->refining;
    }

    /**
     * Register a callback to modify the state when filters are being applied.
     *
     * @param  (Closure(mixed...):$this|void)  $filtering
     * @return $this
     */
    public function whenFiltering($filtering)
    {
        $this->filtering = $filtering;

        return $this;
    }

    /**
     * Get the callback to modify the state when filters are being applied.
     *
     * @return (Closure(mixed...):$this|void)|null
     */
    public function getFilteringCallback()
    {
        return $this->filtering;
    }

    /**
     * Register a callback to modify the state when searching is being applied.
     *
     * @param  (Closure(mixed...):$this|void)  $searching
     * @return $this
     */
    public function whenSearching($searching)
    {
        $this->searching = $searching;

        return $this;
    }

    /**
     * Get the callback to modify the state when searching is being applied.
     *
     * @return (Closure(mixed...):$this|void)|null
     */
    public function getSearchingCallback()
    {
        return $this->searching;
    }
}
