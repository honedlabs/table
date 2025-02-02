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
     * The name of the cookie for remembering column preferences.
     * 
     * @var string
     */
    protected $cookie;

    /**
     * Whether to use cookies to remember column preferences.
     * 
     * @var bool|null
     */
    protected $remember;

    /**
     * The duration that this table's cookie should be remembered for.
     * 
     * @var int
     */
    protected $duration = 60 * 60 * 24 * 30 * 365;

    /**
     * The name of the query parameter to use for toggling columns.
     * 
     * @var string
     */
    protected $toggleKey;

    /**
     * Whether to enable toggling of column visibility for this table.
     * 
     * @var bool
     */
    protected $toggle;
    
    /**
     * Indicates if all tables have toggleable columns.
     *
     * @var bool
     */
    protected static $toggleable = false;

    /**
     * Indicates if all tables remember column visibility.
     *
     * @var bool
     */
    protected static $remembers = false;


    /**
     * Configure the default duration of the cookie to use for all tables.
     */
    public static function remembers(bool $remembers = true): void
    {
        static::$remembers = $remembers;
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
