<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Action\Concerns\HasParameterNames;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\HasAlias;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsHidden;
use Honed\Core\Concerns\IsKey;
use Honed\Core\Concerns\Transformable;
use Honed\Core\Primitive;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * @extends Primitive<string, mixed>
 */
class Column extends Primitive
{
    use Allowable;
    use Concerns\HasClass;
    use Concerns\IsSearchable;
    use Concerns\IsSortable;
    use Concerns\IsToggleable;
    use HasAlias;
    use HasIcon;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasParameterNames;
    use HasType;
    use IsActive;
    use IsHidden;
    use IsKey;
    use Transformable;

    /**
     * The value to display when the column is empty.
     *
     * @var string|null
     */
    protected $fallback;

    /**
     * How the column value is retrieved.
     *
     * @var \Closure|null
     */
    protected $using;

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
        $this->type('column');
    }

    /**
     * Set the fallback value for the column.
     *
     * @param  string|null  $fallback
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
     * @return string|null
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * Set how the column value is retrieved.
     *
     * @param  \Closure|null  $using
     * @return $this
     */
    public function using($using)
    {
        $this->using = $using;

        return $this;
    }

    /**
     * Get how the column value is retrieved.
     *
     * @return \Closure|null
     */
    public function getUsing()
    {
        return $this->using;
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
     * Get the value of the column to form a record.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array<string,mixed>
     */
    public function createRecord($model)
    {
        $using = $this->getUsing();

        $value = $using
            ? $this->evaluate($using, ...static::getModelParameters($model))
            : Arr::get($model, $this->getName());

        return [
            $this->getParameter() => $this->apply($value),
        ];
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
            'meta' => $this->getMeta(),
            'sort' => $this->isSortable() ? $this->sortToArray() : null,
        ];
    }
}
