<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Illuminate\Support\Number;

class CurrencyColumn extends Column
{
    /**
     * The currency to use.
     *
     * @var string|null
     */
    protected $currency;

    /**
     * The locale to use.
     *
     * @var string|null
     */
    protected $locale;

    /**
     * {@inheritdoc}
     */
    public function defineType()
    {
        return 'currency';
    }

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
            $this->getCurrency() ?? '',
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
        // @phpstan-ignore-next-line
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
        return $this->currency;
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
        return $this->locale;
    }
}
