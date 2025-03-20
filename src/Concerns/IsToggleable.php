<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Contracts\ShouldRemember;
use Honed\Table\Contracts\ShouldToggle;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 */
trait IsToggleable
{
    /**
     * Whether the table should allow for toggling which columns are visible.
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
     * @param  bool  $toggle
     * @return $this
     */
    public function toggle($toggle = true)
    {
        $this->toggle = $toggle;

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

        if ($this instanceof ShouldToggle || $this instanceof ShouldRemember) {
            return true;
        }

        return static::fallbackToggleable();
    }

    /**
     * Determine whether the table should allow the user to toggle which columns
     * are visible from the config.
     *
     * @return bool
     */
    public static function fallbackToggleable()
    {
        return (bool) config('table.toggle', false);
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

        return static::fallbackColumnsKey();
    }

    /**
     * Get the query parameter for which columns to display from the config.
     *
     * @return string
     */
    public static function fallbackColumnsKey()
    {
        return type(config('table.columns_key', 'columns'))->asString();
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
            return (bool) $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return static::fallbackRememberable();
    }

    /**
     * Determine whether the table should remember the user preferences from
     * the config.
     *
     * @return bool
     */
    public static function fallbackRememberable()
    {
        return (bool) config('table.remember', false);
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
     * Get the cookie name to use for the table toggle.
     *
     * @return string
     */
    public function getCookieName()
    {
        if (isset($this->cookieName)) {
            return $this->cookieName;
        }

        return static::guessCookieName();
    }

    /**
     * Guess the name of the cookie to use for remembering the columns to
     * display.
     *
     * @return string
     */
    public static function guessCookieName()
    {
        return Str::of(static::class)
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

        return static::fallbackDuration();
    }

    /**
     * Get the duration of the cookie to use for remembering the columns to
     * display from the config.
     *
     * @return int
     */
    public static function fallbackDuration()
    {
        return type(config('table.duration', 15768000))->asInt();
    }

    /**
     * Use the columns cookie to determine which columns are active, or set the
     * cookie to the current columns.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  array<int,string>|null  $params
     * @return array<int,string>|null
     */
    public function configureCookie($request, $params)
    {
        if (filled($params)) {
            Cookie::queue(
                $this->getCookieName(),
                \json_encode($params),
                $this->getDuration()
            );

            return $params;
        }

        $value = $request->cookie($this->getCookieName(), null);

        if (! \is_string($value)) {
            return $params;
        }

        /** @var array<int,string>|null */
        return \json_decode($value, false);
    }
}
