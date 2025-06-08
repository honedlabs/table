<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Closure;
use Illuminate\Support\Number;

class CurrencyColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'currency';

    /**
     * The currency to use.
     *
     * @var string|null
     */
    protected $currency;

    /**
     * The default currency to use.
     *
     * @var string|\Closure|null
     */
    protected static $useCurrency;

    /**
     * The locale to use.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * The default locale to use.
     *
     * @var string|\Closure|null
     */
    protected static $useLocale;

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        if (\is_null($value) || ! \is_numeric($value)) {
            return $this->getFallback();
        }

        $value = (float) $value;

        return Number::currency(
            $value,
            $this->getCurrency(),
            $this->getLocale()
        );
    }

    /**
     * Set the transformer to convert the value from cents to dollars.
     *
     * @return $this
     */
    public function cents()
    {
        $this->transformer(fn ($value) => $value / 100);

        return $this;
    }

    /**
     * Set the currency to use.
     *
     * @param  string  $currency
     * @return $this
     */
    public function currency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get the currency to use.
     *
     * @return string|null
     */
    public function getCurrency()
    {
        return $this->currency ??= $this->usesCurrency();
    }

    /**
     * Set the default currency to use.
     *
     * @param  string|\Closure(mixed...):string  $currency
     */
    public static function useCurrency($currency)
    {
        static::$useCurrency = $currency;
    }

    /**
     * Get the default currency to use.
     *
     * @return string|null
     */
    protected function usesCurrency()
    {
        if (is_null(static::$useCurrency)) {
            return null;
        }

        if (static::$useCurrency instanceof Closure) {
            static::$useCurrency = $this->evaluate($this->useCurrency);
        }

        return static::$useCurrency;
    }

    /**
     * Set the locale to use.
     *
     * @param  string  $locale
     * @return $this
     */
    public function locale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get the locale to use.
     *
     * @return string|null
     */
    public function getLocale()
    {
        return $this->locale ??= $this->usesLocale();
    }

    /**
     * Set the default locale to use.
     *
     * @param  string|\Closure(mixed...):string  $locale
     */
    public static function useLocale($locale)
    {
        static::$useLocale = $locale;
    }

    /**
     * Get the default locale to use.
     *
     * @return string|null
     */
    protected function usesLocale()
    {
        if (is_null(static::$useLocale)) {
            return null;
        }

        if (static::$useLocale instanceof Closure) {
            static::$useLocale = $this->evaluate($this->useLocale);
        }

        return static::$useLocale;
    }
}
