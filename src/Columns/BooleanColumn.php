<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Closure;

class BooleanColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'boolean';

    /**
     * The label to display when the value evaluates to true.
     *
     * @var string|null
     */
    protected $trueLabel;

    /**
     * The default label to use for the true value.
     *
     * @var string|\Closure
     */
    protected static $useTrueLabel = 'True';

    /**
     * The label to display when the value evaluates to false.
     *
     * @var string|null
     */
    protected $falseLabel;

    /**
     * The default label to use for the false value.
     *
     * @var string|\Closure
     */
    protected static $useFalseLabel = 'False';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        return (bool) $value ? $this->getTrueLabel() : $this->getFalseLabel();
    }

    /**
     * Set the label for the true value.
     *
     * @param  string  $true
     * @return $this
     */
    public function trueLabel($true)
    {
        $this->trueLabel = $true;

        return $this;
    }

    /**
     * Get the label for the true value.
     *
     * @return string
     */
    public function getTrueLabel()
    {
        return $this->trueLabel ??= $this->usesTrueLabel();
    }

    /**
     * Set the default label to use for the true value.
     *
     * @param  string|\Closure(mixed...):string  $trueLabel
     * @return void
     */
    public static function useTrueLabel($trueLabel)
    {
        static::$useTrueLabel = $trueLabel;
    }

    /**
     * Get the default label to use for the true value.
     *
     * @return string|null
     */
    protected function usesTrueLabel()
    {
        if (is_null(static::$useTrueLabel)) {
            return null;
        }

        if (static::$useTrueLabel instanceof Closure) {
            static::$useTrueLabel = $this->evaluate($this->useTrueLabel);
        }

        return static::$useTrueLabel;
    }

    /**
     * Set the label for the false value.
     *
     * @param  string  $false
     * @return $this
     */
    public function falseLabel($false)
    {
        $this->falseLabel = $false;

        return $this;
    }

    /**
     * Get the label for the false value.
     *
     * @return string
     */
    public function getFalseLabel()
    {
        return $this->falseLabel ??= $this->usesFalseLabel();
    }

    /**
     * Set the default label to use for the false value.
     *
     * @param  string|\Closure(mixed...):string  $falseLabel
     * @return void
     */
    public static function useFalseLabel($falseLabel)
    {
        static::$useFalseLabel = $falseLabel;
    }

    /**
     * Get the default label to use for the false value.
     *
     * @return string|null
     */
    protected function usesFalseLabel()
    {
        if (is_null(static::$useFalseLabel)) {
            return null;
        }

        if (static::$useFalseLabel instanceof Closure) {
            static::$useFalseLabel = $this->evaluate($this->useFalseLabel);
        }

        return static::$useFalseLabel;
    }

    /**
     * @param  string  $true
     * @param  string  $false
     * @return $this
     */
    public function labels($true, $false)
    {
        $this->trueLabel($true);
        $this->falseLabel($false);

        return $this;
    }

    /**
     * Set the default labels to use for the true and false values.
     *
     * @param  string|\Closure(mixed...):string  $true
     * @param  string|\Closure(mixed...):string  $false
     * @return void
     */
    public static function useLabels($true = 'True', $false = 'False')
    {
        static::$useTrueLabel = $true;
        static::$useFalseLabel = $false;
    }
}
