<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Honed\Table\Columns\Contracts\Column;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Stringable;
use Illuminate\Support\Facades\Cookie;

trait Toggleable
{
    const RememberDuration = 60 * 24 * 30 * 365; // 1 year

    const RememberName = 'cols';

    /**
     * The name of this table's cookie for remembering column visibility
     * 
     * @var string
     */
    protected $cookie;

    /**
     * Whether the table has cookies enabled.
     */
    protected $cookies;

    /**
     * Whether the table has cookies enabled by default.
     */
    protected static $withCookies = true;

    /**
     * The duration that this table's cookie should be remembered for
     * 
     * @var int|null
     */
    protected $duration;

    /**
     * The duration of the cookie to use for all tables.
     * 
     * @var int|null
     */
    protected static $cookieRemember = self::RememberDuration;

    /**
     * The name of the query parameter to use for toggling columns.
     * 
     * @var string
     */
    protected $remember;

    /**
     * The name to use for the query parameter to toggle visibility for all tables.
     * 
     * @var string
     */
    protected static $rememberName = self::RememberName;

    /**
     * Whether to enable toggling of column visibility for this table.
     * 
     * @var bool
     */
    protected $toggle;

    /**
     * Whether to enable toggling of column visibility for all tables.
     * 
     * @var bool
     */
    protected static $defaultToggle = false;

    /**
     * Configure the default duration of the cookie to use for all tables.
     */
    public static function rememberCookieFor(int|null $seconds): void
    {
        static::$cookieRemember = $seconds;
    }

    /**
     * Configure the name of the query parameter to use for toggling columns.
     */
    public static function rememberCookieAs(string $name = null): void
    {
        static::$rememberName = $name ?? self::RememberName;
    }

    /**
     * Configure whether to enable toggling of columns for all tables by default.
     */
    public static function alwaysToggleable(bool $toggle = true): void
    {
        static::$defaultToggle = $toggle;
    }

    /**
     * Configure whether to enable cookies for all tables by default.
     */
    public static function useCookies(bool $cookies = true): void
    {
        static::$withCookies = $cookies;
    }

    /**
     * Set as toggleable quietly.
     */
    public function setToggleable(bool $toggle): void
    {
        $this->toggle = $toggle;
    }

    /**
     * Get the cookie name to use for the table toggle.
     */
    public function getCookieName(): string
    {
        return \property_exists($this, 'cookie') && ! \is_null($this->cookie)
            ? $this->cookie
            : $this->getDefaultCookie();
    }

    /**
     * Determine whether the table has cookies enabled.
     */
    public function useCookie(): bool
    {
        return (bool) (\property_exists($this, 'cookies') && ! \is_null($this->cookies))
            ? $this->cookies
            : static::$withCookies;
    }

    /**
     * Get the default cookie name to use for the table.
     */
    public function getDefaultCookie(): string
    {
        return (new Stringable(static::class))
            ->classBasename()
            ->append('Table')
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Get the default duration of the cookie to use for the table toggle.
     */
    public function getRememberDuration(): int
    {
        return match (true) {
            ! \property_exists($this, 'duration') || \is_null($this->duration) => static::$cookieRemember,
            $this->duration > 0 => $this->duration,
            default => 5 * 365 * 24 * 60 * 60,
        };
    }

    /**
     * Get the query parameter to use for toggling columns.
     */
    public function getRememberName(): string
    {
        return \property_exists($this, 'remember') && ! \is_null($this->remember)
            ? $this->remember
            : static::$rememberName;
    }

    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isToggleable(): bool
    {
        return (bool) (\property_exists($this, 'toggle') && ! \is_null($this->toggle))
            ? $this->toggle
            : static::$defaultToggle;
    }

    /**
     * Update the cookie with the new data.
     * 
     * @param mixed $data
     */
    public function enqueueCookie($data): void
    {
        Cookie::queue(
            $this->getCookieName(), 
            \json_encode($data), 
            $this->getRememberDuration()
        );
    }

    /**
     * Get the data stored in the cookie.
     * 
     * @param \Illuminate\Http\Request $request
     */
    public function retrieveCookie($request = null): mixed
    {
        return \json_decode(
            ($request ?? request())->cookie($this->getCookieName(), '[]'),
            true
        );
    }

    /**
     * Resolve parameters using cookies if applicable.
     *
     * @param array<int,string>|null $params
     * @param \Illuminate\Http\Request|null $request
     * @return array<int,string>|null
     */
    protected function resolveCookieParams($params, $request): ?array
    {
        if (! \is_null($params)) {
            $this->enqueueCookie($params);
            return $params;
        }

        return $this->retrieveCookie($request);
    }

    /**
     * Get the columns to show.
     *
     * @return array<int,string>|null
     */
    public function toggleParameters(Request $request = null): ?array
    {
        $params = ($request ?? request())
            ->string($this->getRememberName())
            ->trim()
            ->remove(' ')
            ->explode(',')
            ->filter(fn($param) => $param !== '') // Filter out empty strings
            ->toArray();

        return empty($params) ? null : $params;
    }

    /**
     * Toggle the columns for the request.
     * 
     * @param \Illuminate\Support\Collection<\Honed\Table\Columns\Contracts\Column> $columns
     * @return \Illuminate\Support\Collection<\Honed\Table\Columns\Contracts\Column>
     */
    public function toggleColumns(Collection $columns, Request $request = null)
    {
        if (! $this->isToggleable()) {
            // All columns are active by default using `setUp()`
            return $columns;
        }

        
        $params = $this->toggleParameters($request);
        
        if ($this->useCookie()) {
            $params = $this->resolveCookieParams($params, $request);
        }

        return $columns
            ->when(! \is_null($params),
                fn (Collection $columns) => $columns
                    ->filter(static fn (BaseColumn $column) => $column
                        ->active(!$column->isToggleable() || \in_array($column->getName(), $params))
                        ->isActive()
                    )->values()
            );
    }
}
