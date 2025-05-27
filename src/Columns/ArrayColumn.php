<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class ArrayColumn extends Column
{
    /**
     * The property to use for the values.
     *
     * @var string|null
     */
    protected $pluck;

    /**
     * The glue to use when joining the array values.
     *
     * @var string|null
     */
    protected $glue;

    /**
     * {@inheritdoc}
     */
    protected $type = 'array';

    /**
     * {@inheritdoc}
     */
    public function formatValue($value)
    {
        if ($value instanceof Arrayable) {
            $value = $value->toArray();
        }

        if (\is_null($value) || ! \is_array($value)) {
            return $this->getFallback();
        }

        if ($pluck = $this->getPluck()) {
            $value = Arr::pluck($value, $pluck);
        }

        if ($glue = $this->getGlue()) {
            return \implode($glue, $value);
        }

        return $value;
    }

    /**
     * Set the glue to use when joining the array values.
     *
     * @param  string  $glue
     * @return $this
     */
    public function glue($glue)
    {
        $this->glue = $glue;

        return $this;
    }

    /**
     * Get the glue to use when joining the array values.
     *
     * @return string|null
     */
    public function getGlue()
    {
        return $this->glue;
    }

    /**
     * Set the column to pluck from the model.
     *
     * @param  string  $pluck
     * @return $this
     */
    public function pluck($pluck)
    {
        $this->pluck = $pluck;

        return $this;
    }

    /**
     * Get the column to pluck from the model.
     *
     * @return string|null
     */
    public function getPluck()
    {
        return $this->pluck;
    }
}
