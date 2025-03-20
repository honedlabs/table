<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Illuminate\Support\Number;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Column<TModel, TBuilder>
 */
class NumberColumn extends Column
{
    /**
     * The number of decimal places to display.
     *
     * @var int|null
     */
    protected $decimals;

    /**
     * Whether to abbreviate the number.
     *
     * @var bool
     */
    protected $abbreviate = false;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();
        $this->type('number');
    }

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        if (\is_null($value) || ! \is_numeric($value)) {
            return $this->getFallback();
        }

        $decimals = $this->getDecimals();
        $abbreviate = $this->isAbbreviated();

        return match (true) {
            isset($decimals) => \number_format((float) $value, $decimals),
            $abbreviate => Number::abbreviate((int) $value),
            default => $value,
        };
    }

    /**
     * Set whether to abbreviate the number.
     *
     * @param  bool  $abbreviate
     * @return $this
     */
    public function abbreviate($abbreviate = true)
    {
        $this->abbreviate = $abbreviate;

        return $this;
    }

    /**
     * Determine if the number should be abbreviated.
     *
     * @return bool
     */
    public function isAbbreviated()
    {
        return $this->abbreviate;
    }

    /**
     * Set the number of decimal places to display.
     *
     * @param  int|null  $decimals
     * @return $this
     */
    public function decimals($decimals = null)
    {
        $this->decimals = $decimals;

        return $this;
    }

    /**
     * Determine the number of decimal places to display.
     *
     * @return int|null
     */
    public function getDecimals()
    {
        return $this->decimals;
    }
}
