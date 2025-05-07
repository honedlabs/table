<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Carbon\Carbon;
use Honed\Core\Interpret;

class DateColumn extends Column
{
    /**
     * Whether to use diffForHumans.
     *
     * @var bool
     */
    protected $diffForHumans = false;

    /**
     * A format to use for the date.
     *
     * @var string|null
     */
    protected $format;

    /**
     * The timezone to use for date parsing.
     *
     * @var string|null
     */
    protected $timezone;

    /**
     * {@inheritdoc}
     */
    public function defineType()
    {
        return 'date';
    }

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

        if (! $value instanceof Carbon) {
            $value = Interpret::dateOf($value, $this->getTimezone());
        }

        if (\is_null($value)) {
            return $this->getFallback();
        }

        if ($this->isDiffForHumans()) {
            return $value->diffForHumans();
        }

        return $value->format($this->getBuildermat() ?? 'Y-m-d H:i:s');
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
     * Determine if the date should be formatted using diffForHumans.
     *
     * @return bool
     */
    public function isDiffForHumans()
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
     * @return string|null
     */
    public function getBuildermat()
    {
        return $this->format;
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
        return $this->timezone ?? config('app.timezone');
    }
}
