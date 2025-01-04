<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cookie;

trait Toggleable
{
    /**
     * The name of this table's cookie for remembering column visibility
     * @var string
     */
    // protected $cookie;

    /**
     * The duration that this table's cookie should be remembered for
     * 
     * @var int|null
     */
    // protected $duration;

    /**
     * The duration of the cookie to use for all tables.
     * @var int|null
     */
    protected static $cookieRemember = 60 * 24 * 30 * 365; // 1 year

    /**
     * The name of the query parameter to use for toggling columns.
     * 
     * @var string
     */
    // protected $remember;

    /**
     * The name to use for the query parameter to toggle visibility for all tables.
     * 
     * @var string
     */
    protected static $rememberName = 'cols';

    /**
     * Whether to enable toggling of column visibility for this table.
     * 
     * @var bool
     */
    // protected $toggle;

    /**
     * Whether to enable toggling of column visibility for all tables.
     * 
     * @var bool
     */
    protected static $defaultToggle = false;

    /**
     * Configure the default duration of the cookie to use for all tables.
     *
     * @param  int|null  $seconds  The duration in seconds
     */
    public static function rememberCookieFor(int|null $seconds): void
    {
        static::$cookieRemember = $seconds;
    }

    /**
     * Configure the name of the query parameter to use for toggling columns.
     *
     * @param  string  $name  The name of the query parameter
     */
    public static function rememberCookieAs(string $name): void
    {
        static::$rememberName = $name;
    }

    /**
     * Configure whether to enable toggling of columns for all tables by default.
     *
     * @param  bool  $toggle  Whether to enable toggling of columns
     */
    public static function alwaysToggleable(bool $toggle = true): void
    {
        static::$defaultToggle = $toggle;
    }

    /**
     * Get the cookie name to use for the table toggle.
     */
    public function getCookieName(): string
    {
        return \property_exists($this, 'cookie')
            ? $this->cookie
            : $this->getDefaultCookie();
    }

    /**
     * Get the default cookie name to use for the table.
     */
    public function getDefaultCookie(): string
    {
        return str(\class_basename($this))
            ->lower()
            ->kebab()
            ->toString();
    }

    /**
     * Get the default duration of the cookie to use for the table toggle.
     *
     * @return int
     */
    public function getRememberDuration()
    {
        return \property_exists($this, 'duration')
            ? $this->duration
            : static::$cookieRemember;
    }

    /**
     * Get the query parameter to use for toggling columns.
     */
    public function getRememberName(): string
    {
        return \property_exists($this, 'remember')
            ? $this->remember
            : static::$rememberName;
    }

    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isToggleable(): bool
    {
        return (bool) \property_exists($this, 'toggle')
            ? $this->toggle
            : static::$defaultToggle;
    }

    /**
     * Update the cookie with the new data.
     *
     * @param  mixed  $data
     */
    public function setCookie(mixed $data): void
    {
        Cookie::queue($this->getCookieName(), \json_encode($data), $this->getRememberDuration());
    }

    /**
     * Get the data stored in the cookie.
     *
     * @return mixed
     */
    public function getCookie(): mixed
    {
        return \json_decode(request()->cookie($this->getCookieName(), null), true);
    }

    /**
     * Get the columns to show from the request.
     *
     * @param  \Illuminate\Http\Request|null  $request
     * @return array<int,string>
     */
    public function getToggleParameters(?Request $request = null): array
    {
        $request = $request ?? request();

        return $request->string($this->getToggleName())
            ->trim()
            ->remove(' ')
            ->explode(',')
            ->toArray();
    }

    /**
     * Apply the toggleability to determine which columns to show.
     */
    public function toggleColumns(): void
    {
        $activeColumns = $this->getToggleParameters();
        // TODO
        $this->getColumns()
            ->each(fn (BaseColumn $column) => $column->setActive(true));
    }
}
