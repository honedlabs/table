<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

class TextColumn extends Column
{
    /**
     * The prefix to display.
     *
     * @var string|null
     */
    protected $prefix;

    /**
     * The suffix to display.
     *
     * @var string|null
     */
    protected $suffix;

    /**
     * The number of characters to display.
     *
     * @var int|null
     */
    protected $length;

    /**
     * {@inheritdoc}
     */
    public function defineType()
    {
        return 'text';
    }

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        if (\is_null($value)) {
            return $this->getFallback();
        }

        $value = type($value)->asString();

        $prefix = $this->getPrefix();
        $suffix = $this->getSuffix();
        $length = $this->getLength();

        if (! \is_null($prefix)) {
            $value = $prefix.$value;
        }

        if (! \is_null($suffix)) {
            $value = $value.$suffix;
        }

        if (! \is_null($length)) {
            $value = substr($value, 0, $length);
        }

        return $value;
    }

    /**
     * Set the prefix to display.
     *
     * @param  string  $prefix
     * @return $this
     */
    public function prefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get the prefix to display.
     *
     * @return string|null
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set the suffix to display.
     *
     * @param  string  $suffix
     * @return $this
     */
    public function suffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get the suffix to display.
     *
     * @return string|null
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set the number of characters to display.
     *
     * @param  int  $length
     * @return $this
     */
    public function length($length)
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Get the number of characters to display.
     *
     * @return int|null
     */
    public function getLength()
    {
        return $this->length;
    }
}
