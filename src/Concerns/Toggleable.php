<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

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
    protected static $globalDuration = 60 * 24 * 30 * 365; // 1 year

    /**
     * @var string
     */
    protected $toggleAs;

    /**
     * @var string
     */
    protected static $globalToggleAs = 'cols';

    /**
     * @var bool
     */
    protected $toggleable;

    /**
     * @var bool
     */
    protected static $globalToggleable = false;

    /**
     * Configure the default duration of the cookie to use for all tables.
     * 
     * @param int $duration
     * @return void
     */
    public static function setDuration(int $duration)
    {
        static::$globalDuration = $duration;
    }

    /**
     * Configure the default query parameter to use for toggling columns.
     * 
     * @param string $toggleAs
     * @return void
     */
    public static function setToggleAs(string $toggleAs)
    {
        static::$globalToggleAs = $toggleAs;
    }

    /**
     * Configure whether to enable toggling of columns for all tables by default.
     * 
     * @param bool $toggle
     * @return void
     */
    public static function enableToggleable(bool $toggleable = true)
    {
        static::$globalToggleable = $toggleable;
    }

    /**
     * Get the cookie name to use for the table toggle.
     * 
     * @return string
     */
    public function getCookie()
    {
        return $this->inspect('cookie', $this->getDefaultCookie());
    }

    /**
     * Get the default cookie name to use for the table.
     * 
     * @return string
     */
    protected function getDefaultCookie()
    {
        return str(class_basename($this))
            ->slug()
            ->append('_toggle')
            ->toString();
    }

    /**
     * Get the default duration of the cookie to use for the table toggle.
     * 
     * @return int
     */
    public function getDuration()
    {
        return $this->inspect('duration', static::$globalDuration);
    }

    /**
     * Get the query parameter to use for toggling columns.
     * 
     * @return string
     */
    public function getToggleAs()
    {
        return $this->inspect('toggleAs', static::$globalToggleAs);
    }

    /**
     * Determine whether this table has toggling of the columns enabled.
     * 
     * @return bool
     */
    public function isToggleable()
    {
        return $this->inspect('toggleable', static::$globalToggleable);
    }

    /**
     * Get the query parameter to use for toggling columns from the request query parameters.
     * 
     * @return array<int,string>|null
     */
    public function getToggledColumnsTerm()
    {
        $value = request()->input($this->getToggleAs());
        
        if (is_null($value)) {
            return null;
        }

        return array_filter(explode(',', (string) $value));
    }

    /**
     * Encode the data to be stored in the cookie.
     * 
     * @param array<int,string> $data
     * @return void
     */
    public function encodeCookie(array $data): void
    {
        Cookie::queue($this->getCookie(), json_encode($data), $this->getDuration());
    }

    /**
     * Decode the data stored in the cookie.
     * 
     * @return array<int,string>|null
     */
    public function decodeCookie(): ?array
    {
        return json_decode(request()->cookie($this->getCookie(), null), true);
    }

    /**
     * Get the columns which need to be toggled, and set if needed.
     * 
     * @return array<int,string>|null
     */
    public function getToggledColumns()
    {
        $live = $this->getToggledColumnsTerm();

        if (empty($live)) {
            return $this->decodeCookie();
        }

        $this->encodeCookie($live);

        return $live;
    }
}