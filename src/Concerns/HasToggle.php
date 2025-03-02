<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\Column;
use Honed\Table\Contracts\ShouldRemember;
use Honed\Table\Contracts\ShouldToggle;
use Illuminate\Support\Facades\Cookie;

trait HasToggle
{
    /**
     * Whether the table should allow the user to toggle which columns are
     * displayed.
     *
     * @var bool|null
     */
    protected $toggle;

    /**
     * The query parameter for which columns to display.
     *
     * @var string|null
     */
    protected $columnsKey;

    /**
     * Whether the table should remember the columns to display.
     *
     * @var bool|null
     */
    protected $remember;

    /**
     * The name of the cookie to use for remembering the columns to display.
     *
     * @var string|null
     */
    protected $cookieName;

    /**
     * The duration of the cookie to use for remembering the columns to display.
     *
     * @var int|null
     */
    protected $duration;

    /**
     * Set whether the table should allow the user to toggle which columns are
     * displayed.
     *
     * @param  bool  $toggleable
     * @return $this
     */
    public function toggleable($toggleable = true)
    {
        $this->toggle = $toggleable;

        return $this;
    }

    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible.
     *
     * @return bool
     */
    public function isToggleable()
    {
        if (isset($this->toggle)) {
            return $this->toggle;
        }

        if ($this instanceof ShouldToggle) {
            return true;
        }

        return $this->fallbackToggleable();
    }

    /**
     * Set the query parameter for which columns to display.
     *
     * @param  string  $columnsKey
     * @return $this
     */
    public function columnsKey($columnsKey): static
    {
        $this->columnsKey = $columnsKey;

        return $this;
    }

    /**
     * Get the query parameter for which columns to display.
     *
     * @return string
     */
    public function getColumnsKey()
    {
        if (isset($this->columnsKey)) {
            return $this->columnsKey;
        }

        return $this->fallbackColumnsKey();
    }

    /**
     * Set whether the table should remember the user preferences.
     *
     * @param  bool  $remember
     * @return $this
     */
    public function remember($remember = true)
    {
        $this->remember = $remember;

        return $this;
    }

    /**
     * Determine whether the table should remember the user preferences.
     *
     * @return bool
     */
    public function isRememberable()
    {
        if (isset($this->remember)) {
            return $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return $this->fallbackRememberable();
    }

    /**
     * Get the cookie name to use for the table toggle.
     *
     * @return string
     */
    public function getCookieName()
    {
        if (isset($this->cookieName)) {
            return $this->cookieName;
        }

        return $this->guessCookieName();
    }

    /**
     * Set the cookie name to use for the table toggle.
     *
     * @param  string  $cookieName
     * @return $this
     */
    public function cookieName($cookieName)
    {
        $this->cookieName = $cookieName;

        return $this;
    }

    /**
     * Guess the name of the cookie to use for remembering the columns to
     * display.
     *
     * @return string
     */
    public function guessCookieName()
    {
        return str(static::class)
            ->classBasename()
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Set the duration of the cookie to use for remembering the columns to
     * display.
     *
     * @param  int  $seconds
     * @return $this
     */
    public function duration($seconds)
    {
        $this->duration = $seconds;

        return $this;
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display.
     *
     * @return int
     */
    public function getDuration()
    {
        if (isset($this->duration)) {
            return $this->duration;
        }

        return $this->fallbackDuration();
    }

    /**
     * Toggle the columns that are displayed.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,\Honed\Table\Columns\Column>  $columns
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function toggleColumns($request, $columns)
    {
        if (! $this->isToggleable()) {
            return $columns;
        }

        $params = $this->getToggledColumns($request);

        if ($this->isRememberable()) {
            $params = $this->configureCookie($request, $params);
        }

        return collect($columns)
            ->filter(static function (Column $column) use ($params) {
                $active = $column->isDisplayed($params);
                $column->active($active);

                return $active;
            })
            ->values()
            ->all();
    }

    /**
     * Get the toggled columns from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array<int,string>|null
     */
    public function getToggledColumns($request)
    {
        /** @var string */
        $key = $this->formatScope($this->getColumnsKey());

        $columns = $request->safeArray($key, null, $this->getDelimiter());

        if (\is_null($columns) || $columns->isEmpty()) {
            return null;
        }

        return $columns
            ->map(\trim(...))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * Use the columns cookie to determine which columns are active, or set the
     * cookie to the current columns.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,string>|null  $params
     * @return array<int,string>|null
     */
    protected function configureCookie($request, $params)
    {
        if (filled($params)) {
            $this->enqueueCookie($params);

            return $params;
        }

        return $this->dequeueCookie($request, $params);
    }

    /**
     * Get the query parameter for which columns to display.
     *
     * @return string
     */
    protected function fallbackColumnsKey()
    {
        return type(config('table.config.columns', 'columns'))->asString();
    }

    /**
     * Determine whether the table should remember the user preferences.
     *
     * @return bool
     */
    protected function fallbackRememberable()
    {
        return (bool) config('table.toggle.remember', false);
    }

    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible.
     *
     * @return bool
     */
    protected function fallbackToggleable()
    {
        return (bool) config('table.toggle.enabled', false);
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display.
     *
     * @return int
     */
    protected function fallbackDuration()
    {
        return type(config('table.toggle.duration', 15768000))->asInt();
    }

    /**
     * Enqueue a new cookie with preference data.
     *
     * @param  array<int,string>  $params
     * @return void
     */
    protected function enqueueCookie($params)
    {
        Cookie::queue(
            $this->getCookieName(),
            \json_encode($params),
            $this->getDuration()
        );
    }

    /**
     * Retrieve the preference data from the cookie if it exists.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,string>|null  $params
     * @return array<int,string>|null
     */
    protected function dequeueCookie($request, $params)
    {
        $value = $request->cookie($this->getCookieName(), null);

        if (! \is_string($value)) {
            return $params;
        }

        /** @var array<int,string>|null */
        return \json_decode($value, false);
    }
}
