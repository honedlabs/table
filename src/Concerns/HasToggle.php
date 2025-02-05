<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Honed\Table\Columns\BaseColumn;
use Honed\Table\Columns\Column;
use Honed\Table\Contracts\ShouldRemember;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cookie;

trait HasToggle
{
    const Duration = 60 * 24 * 30 * 365; // 1 year
    const ColumnsKey = 'columns';

    /**
     * @var bool|null
     */
    protected $toggle;

    /**
     * @var bool|null
     */
    protected $remember;

    /**
     * @var string|null
     */
    protected $cookie;

    /**
     * @var int|null
     */
    protected $duration;

    /**
     * @var string|null
     */
    protected $columnsKey;
    
    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isToggleable(): bool
    {
        if (\property_exists($this, 'toggle') && ! \is_null($this->toggle)) {
            return $this->toggle;
        }

        return false;
    }

    /**
     * Determine whether this table has toggling of the columns enabled.
     */
    public function isRemembering(): bool
    {
        if (\property_exists($this, 'remember') && ! \is_null($this->remember)) {
            return $this->remember;
        }

        if ($this instanceof ShouldRemember) {
            return true;
        }

        return false;
    }

    /**
     * Get the cookie name to use for the table toggle.
     */
    public function getCookie(): string
    {
        if (\property_exists($this, 'cookie') && ! \is_null($this->cookie)) {
            return $this->cookie;
        }

        return str(static::class)
            ->classBasename()
            ->append('Table')
            ->kebab()
            ->lower()
            ->toString();
    }

    /**
     * Get the default duration of the cookie to use for the table toggle.
     */
    public function getDuration(): int
    {
        if (\property_exists($this, 'duration') && ! \is_null($this->duration)) {
            return $this->duration;
        }

        return self::Duration;
    }

    /**
     * Get the query parameter to use for toggling columns.
     */
    public function getColumnsKey(): string
    {
        if (\property_exists($this, 'columnsKey') && ! \is_null($this->columnsKey)) {
            return $this->columnsKey;
        }

        return self::ColumnsKey;
    }

    /**
     * @return array<int,\Honed\Table\Columns\Column>
     */
    public function toggle(): array
    {
        $columns = $this->getColumns();

        if (! $this->isToggleable()) {
            return $columns;
        }

        /** @var \Illuminate\Http\Request */
        $request = $this->getRequest();

        $params = $this->getColumnsFromRequest($request);

        if ($this->isRemembering()) {
            $params = $this->configureCookie($request, $params);
        }

        return \array_filter($columns,
            function (Column $column) use ($params) {
                if (\is_null($params)) {
                    return $column->isKey() || $column->isToggleable();
                }

                return \in_array($column->getName(), $params);
            }
        );
    }

    /**
     * @param array<int,string>|null $params
     * 
     * @return array<int,string>|null
     */
    public function configureCookie(Request $request, ?array $params): ?array
    {
        if (! \is_null($params)) {
            Cookie::queue($this->getCookie(), \json_encode($params), $this->getDuration());

            return $params;
        }

        $params = $request->cookie($this->getCookie(), null);

        if (\is_null($params)) {
            return null;
        }

        /** @var array<int,string> */
        return \json_decode($params, false);
    }

    /**
     * Retrieve the columns to display from the request.
     * 
     * @return array<int,string>|null
     */
    public function getColumnsFromRequest(Request $request): ?array
    {
        $matches = $request->string($this->getColumnsKey(), null);

        if ($matches->isEmpty()) {
            return null;
        }

        /** @var array<int,string> */
        return $matches
            ->explode(',')
            ->map(fn ($v) => \trim($v))
            ->toArray();
    }
}
