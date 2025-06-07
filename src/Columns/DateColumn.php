<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Closure;
use Honed\Core\Interpret;
use Illuminate\Support\Facades\Config;

class DateColumn extends Column
{
    const DEFAULT_FORMAT = 'Y-m-d H:i:s';

    /**
     * {@inheritdoc}
     */
    protected $type = 'date';
    
    /**
     * Whether to use diffForHumans.
     *
     * @var bool|null
     */
    protected $diffForHumans;

    /**
     * A format to use for the date.
     *
     * @var string|null
     */
    protected $format;

    /**
     * The default format to use for the date.
     *
     * @var string|\Closure
     */
    protected static $useFormat = self::DEFAULT_FORMAT;

    /**
     * The timezone to use for date parsing.
     *
     * @var string|null
     */
    protected $timezone;

    /**
     * The default timezone to use for date parsing.
     *
     * @var string|\Closure|null
     */
    protected static $useTimezone;


    /**
     * {@inheritdoc}
     *
     * @param  string|\Carbon\Carbon|null  $value
     */
    public function formatValue($value)
    {
        if (\is_null($value)) {
            return $this->getFallback();
        }

        if (! $value instanceof CarbonInterface) {
            $value = Interpret::dateOf($value);
            
            if (\is_null($value)) {
                return $this->getFallback();
            }
        }

        if ($value instanceof CarbonImmutable) {
            $value = Carbon::instance($value);
        }

        $timezone = $this->getTimezone();
        
        if ($timezone) {
            $value = $value->shiftTimezone($timezone);
        }

        if ($this->diffs()) {
            return $value->diffForHumans();
        }

        return $value->format($this->getFormat());
    }

    /**
     * Use diffForHumans to format the date.
     *
     * @param  bool  $diffForHumans
     * @return $this
     */
    public function diffForHumans($diffForHumans = true)
    {
        $this->diffForHumans = $diffForHumans;

        return $this;
    }

    /**
     * Use diffForHumans to format the date.
     *
     * @param  bool  $diffForHumans
     * @return $this
     */
    public function diff($diffForHumans = true)
    {
        return $this->diffForHumans($diffForHumans);
    }

    /**
     * Determine if the date should be formatted using diffForHumans.
     *
     * @return bool
     */
    public function diffs()
    {
        return (bool) $this->diffForHumans;
    }

    /**
     * Set the format for the date.
     *
     * @param  string  $format
     * @return $this
     */
    public function format($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Get the format for the date.
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format ??= $this->usesFormat();
    }

    /**
     * Set the default format to use for formatting dates.
     * 
     * @param string|\Closure():string $format
     * @return void
     */
    public static function useFormat($format = 'Y-m-d H:i:s')
    {
        static::$useFormat = $format;
    }

    /**
     * Get the default format to use for formatting dates.
     * 
     * @return string
     */
    protected function usesFormat()
    {
        if (is_null(static::$useFormat)) {
            return null;
        }

        if (static::$useFormat instanceof Closure) {
            static::$useFormat = $this->evaluate($this->useFormat);
        }

        return static::$useFormat;
    }

    /**
     * Set the timezone for date parsing.
     *
     * @param  string  $timezone
     * @return $this
     */
    public function timezone($timezone)
    {
        $this->timezone = $timezone;

        return $this;
    }

    /**
     * Get the timezone for date parsing.
     *
     * @return string|null
     */
    public function getTimezone()
    {
        /** @var string|null */
        return $this->timezone 
            ??= $this->usesTimezone() ?? Config::get('app.timezone');
    }

    /**
     * Set the default timezone for all date columns.
     *
     * @param string|\Closure(mixed...):string $timezone
     * @return void
     */
    public static function useTimezone($timezone)
    {
        static::$useTimezone = $timezone;
    }

    /**
     * Get the default timezone to use for date parsing.
     *
     * @return string|null
     */
    protected function usesTimezone()
    {
        if (is_null(static::$useTimezone)) {
            return null;
        }

        if (static::$useTimezone instanceof Closure) {
            static::$useTimezone = $this->evaluate($this->useTimezone);
        }

        return static::$useTimezone;
    }

    public static function flushState()
    {
        parent::flushState();

        static::$useFormat = self::DEFAULT_FORMAT;
        static::$useTimezone = null;
    }
}
