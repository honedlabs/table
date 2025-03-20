<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasAlias;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasQueryClosure;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Primitive;
use Honed\Refine\Sort;
use Honed\Table\Concerns\HasClass;
use Honed\Table\Concerns\IsVisible;
use Illuminate\Support\Str;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @extends Primitive<string, mixed>
 */
class Column extends Primitive
{
    use Allowable;
    use HasAlias;
    use HasClass;
    use HasExtra;
    use HasIcon;
    use HasLabel;
    use HasName;

    /** @use HasQueryClosure<TModel, TBuilder> */
    use HasQueryClosure;

    use HasType;
    use HasValue;
    use IsActive;
    use IsHidden;
    use IsVisible;
    use Transformable;

    /**
     * Whether this column represents the record key.
     *
     * @var bool
     */
    protected $key = false;

    /**
     * The value to display when the column is empty.
     *
     * @var mixed
     */
    protected $fallback;

    /**
     * The column sort.
     *
     * @var \Honed\Refine\Sort<TModel, TBuilder>|null
     */
    protected $sort;

    /**
     * Whether to search on the column.
     *
     * @var bool
     */
    protected $search = false;

    /**
     * Whether to have a simple filter on the column.
     *
     * @var bool
     */
    protected $filter = false;

    /**
     * How to select this column
     *
     * @var string|bool|array<int,string>
     */
    protected $select = true;

    /**
     * Create a new column instance.
     *
     * @param  string  $name
     * @param  string|null  $label
     * @return static
     */
    public static function make($name, $label = null)
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->active(true);
    }

    /**
     * Set this column to represent the record key.
     *
     * @param  bool  $key
     * @return $this
     */
    public function key($key = true)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Determine whether this column represents the record key.
     *
     * @return bool
     */
    public function isKey()
    {
        return $this->key;
    }

    /**
     * Set the fallback value for the column.
     *
     * @param  mixed  $fallback
     * @return $this
     */
    public function fallback($fallback)
    {
        $this->fallback = $fallback;

        return $this;
    }

    /**
     * Get the fallback value for the column.
     *
     * @return mixed
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Determine if the column has a fallback value.
     *
     * @return bool
     */
    public function hasFallback()
    {
        return isset($this->fallback);
    }

    /**
     * Set the column as sortable.
     *
     * @param  \Honed\Refine\Sort<TModel, TBuilder>|string|bool  $sort
     * @return $this
     */
    public function sort($sort = true)
    {
        if (! $sort) {
            $this->sort = null;

            return $this;
        }

        if ($sort instanceof Sort) {
            $this->sort = $sort;

            return $this;
        }

        $name = \is_string($sort) ? $sort : $this->getName();

        $this->sort = Sort::make($name, $this->getLabel())
            ->alias($this->getParameter());

        return $this;
    }

    /**
     * Get the sort.
     *
     * @return \Honed\Refine\Sort<TModel, TBuilder>|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Determine if the column is sortable.
     *
     * @return bool
     */
    public function isSortable()
    {
        return (bool) $this->sort;
    }

    /**
     * Set the column as searchable.
     *
     * @param  bool  $search
     * @return $this
     */
    public function search($search = true)
    {
        $this->search = $search;

        return $this;
    }

    /**
     * Determine if the column is searchable.
     *
     * @return bool
     */
    public function isSearchable()
    {
        return $this->search;
    }

    /**
     * Set the column as filterable.
     *
     * @param  bool  $filter
     * @return $this
     */
    public function filter($filter = true)
    {
        $this->filter = $filter;

        return $this;
    }

    /**
     * Determine if the column is filterable.
     *
     * @return bool
     */
    public function isFilterable()
    {
        return $this->filter;
    }

    /**
     * Set how to select this column.
     *
     * @param  string|bool|array<int,string>  $select
     * @return $this
     */
    public function select($select = true)
    {
        $this->select = $select;

        return $this;
    }

    /**
     * Get the properties to select.
     *
     * @return string|bool|array<int,string>
     */
    public function getSelect()
    {
        return $this->select;
    }

    /**
     * Determine if the column can be selected.
     *
     * @return bool
     */
    public function isSelectable()
    {
        return (bool) $this->select;
    }

    /**
     * Get the parameter for the column.
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->getAlias()
            ?? Str::of($this->getName())
                ->replace('.', '_')
                ->value();
    }

    /**
     * Apply the column's transform and format value.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function apply($value)
    {
        $value = $this->transform($value);

        return $this->formatValue($value);
    }

    /**
     * Format the value of the column.
     *
     * @param  mixed  $value
     * @return mixed
     */
    public function formatValue($value)
    {
        return $value ?? $this->getFallback();
    }

    /**
     * Get the sort instance as an array.
     *
     * @return array<string,mixed>
     */
    public function sortToArray()
    {
        $sort = $this->getSort();

        if (! $sort) {
            return [];
        }

        return [
            'active' => $sort->isActive(),
            'direction' => $sort->getDirection(),
            'next' => $sort->getNextDirection(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'name' => $this->getParameter(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'hidden' => $this->isHidden(),
            'active' => $this->isActive(),
            'toggleable' => $this->isToggleable(),
            'icon' => $this->getIcon(),
            'class' => $this->getClass(),
            'sort' => $this->sortToArray(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function __call($method, $parameters)
    {
        if ($method === 'query') {
            /** @var \Closure(mixed...):void $query */
            $query = $parameters[0];

            return $this->queryClosure($query);
        }

        parent::__call($method, $parameters);
    }
}
