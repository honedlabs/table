<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class BooleanColumn extends Column
{
    /**
     * The label to display when the value evaluates to true.
     *
     * @var string
     */
    protected $trueLabel = 'True';

    /**
     * The label to display when the value evaluates to false.
     *
     * @var string
     */
    protected $falseLabel = 'False';

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->type('boolean');
    }

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
     * Get the label for the true value.
     *
     * @return string
     */
    public function getTrueLabel()
    {
        return $this->trueLabel;
    }

    /**
     * Get the label for the false value.
     *
     * @return string
     */
    public function getFalseLabel()
    {
        return $this->falseLabel;
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
}
