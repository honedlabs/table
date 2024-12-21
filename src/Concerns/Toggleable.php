<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Cookie;

trait Toggleable
{
    /**
     * @var string
     */
    protected $cookie;

    /**
     * @var int
     */
    protected $duration;

    /**
     * @var int
     */
    protected static $cookieDuration = 60 * 24 * 30 * 365; // 1 year

    /**
     * @var string
     */
    protected $toggled;

    /**
     * @var string
     */
    protected static $toggledName = 'cols';

    /**
     * @var bool
     */
    protected $toggle;

    /**
     * @var bool
     */
    protected static $defaultToggle = false;

    /**
     * Configure the default duration of the cookie to use for all tables.
     *
     * @param  int  $seconds  The duration in seconds
     */
    public static function cookieDuration(int $seconds): void
    {
        static::$cookieDuration = $seconds;
    }

    /**
     * Configure the name of the query parameter to use for toggling columns.
     *
     * @param  string  $name  The name of the query parameter
     */
    public static function toggledName(string $name): void
    {
        static::$toggledName = $name;
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
        return $this->inspect('cookie', $this->getDefaultCookie());
    }

    /**
     * Get the default cookie name to use for the table.
     */
    public function getDefaultCookie(): string
    {
        return str(class_basename($this))
            ->lower()
            ->kebab()
            ->toString();
    }

    /**
     * Get the default duration of the cookie to use for the table toggle.
     *
     * @return int
     */
    public function getDuration()
    {
        return $this->inspect('duration', static::$cookieDuration);
    }

    /**
     * Get the query parameter to use for toggling columns.
     */
    public function getToggleName(): string
    {
        return $this->inspect('toggle', static::$toggledName);
    }

    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isToggleable(): bool
    {
        return (bool) $this->inspect('toggle', static::$defaultToggle);
    }

    /**
     * Determine whether this table has toggling of the columns disabled.
     */
    public function isNotToggleable(): bool
    {
        return ! $this->isToggleable();
    }

    /**
     * Update the cookie with the new data.
     *
     * @param  array<int,string>  $data
     */
    public function setCookie(array $data): void
    {
        Cookie::queue($this->getCookieName(), \json_encode($data), $this->getDuration());
    }

    /**
     * Get the data stored in the cookie.
     *
     * @return array<int,string>|null
     */
    public function getCookie(): ?array
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
