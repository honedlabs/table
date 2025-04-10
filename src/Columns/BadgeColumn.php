<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Contracts\HasExtra;
use Illuminate\Support\Arr;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Column<TModel, TBuilder>
 */
class BadgeColumn extends Column implements HasExtra
{
    /**
     * How to map the value to a badge variant.
     *
     * @var array<string,string>|null
     */
    protected $map;

    /**
     * The default badge to use when the value is not mapped.
     *
     * @var string
     */
    protected $default = 'default';

    /**
     * {@inheritdoc}
     */
    public function defineType()
    {
        return 'badge';
    }

    /**
     * Define the extra data for the column.
     *
     * @param  int|string  $value
     * @return array<string,mixed>
     */
    public function extraAs($value)
    {
        $variant = Arr::get(
            $this->getMap(),
            $value,
            $this->getDefault()
        );

        return [
            'variant' => $variant,
        ];
    }

    /**
     * Set the map to use to determine the badge variant.
     *
     * @param  array<string,string>  $map
     * @return $this
     */
    public function map($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * Get the map to use to determine the badge variant.
     *
     * @return array<string,string>
     */
    public function getMap()
    {
        return $this->map ?? [];
    }

    /**
     * Set the default badge to use when the value is not mapped.
     *
     * @param  string  $default
     * @return $this
     */
    public function default($default)
    {
        $this->default = $default;

        return $this;
    }

    /**
     * Get the default badge to use when the value is not mapped.
     *
     * @return string
     */
    public function getDefault()
    {
        return $this->default;
    }
}
