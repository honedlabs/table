<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Closure;
use Honed\Core\Primitive;
use Illuminate\Support\Str;
use Honed\Refine\Sorts\Sort;
use InvalidArgumentException;
use Honed\Core\Concerns\HasIcon;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasAlias;
use Honed\Core\Concerns\HasExtra;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasQuery;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\Allowable;
use Honed\Table\Concerns\Selectable;
use Honed\Refine\Concerns\CanBeHidden;
use Honed\Refine\Concerns\HasQualifier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Infolist\Entries\Concerns\HasState;
use Honed\Infolist\Entries\Concerns\CanBeBadge;
use Honed\Infolist\Entries\Concerns\HasPlaceholder;
use Honed\Infolist\Entries\Concerns\CanBeAggregated;
use Honed\Infolist\Entries\Concerns\CanFormatValues;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Column extends Primitive implements NullsAsUndefined
{
    use Allowable;
    use CanBeAggregated;
    use CanBeHidden;
    use CanFormatValues;
    use CanBeBadge;
    use Concerns\CanBeKey;
    use Concerns\Exportable;
    use Concerns\Filterable;
    use Concerns\HasClasses;
    use Concerns\Searchable;
    use Concerns\Sortable;
    use Concerns\Toggleable;
    use HasAlias;
    use HasExtra;
    use HasIcon;
    use HasLabel;
    use HasName;
    use HasPlaceholder;
    use HasQualifier;

    /** @use \Honed\Core\Concerns\HasQuery<TModel, TBuilder> */
    use HasQuery;

    use HasState;
    use HasType;
    use IsActive;
    use Selectable;

    public const BADGE = 'badge';

    public const COLOR = 'color';

    public const ICON = 'icon';

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'column';

    /**
     * Provide the instance with any necessary setup.
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->active();

        $this->definition($this);
    }

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
     * @experimental
     */
    public function getResolvedState()
    {
        return $this->state;
    }

    /**
     * Get the parameter for the column.
     *
     * @return string
     */
    public function getParameter()
    {
        return $this->getAlias()
            ?? str_replace('.', '-', $this->getName());
    }

    /**
     * Add a count of the related recordss the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function count($relationship = null)
    {
        return $this->addSimpleRelationship($relationship, 'count');
    }

    /**
     * Add a relationship exists as the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function exists($relationship = null)
    {
        return $this->addSimpleRelationship($relationship, 'exists');
    }

    /**
     * Add an average aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string|null  $column
     * @return $this
     */
    public function avg($relationship = null, $column = null)
    {
        return $this->addAggregateRelationship($relationship, $column, 'avg');
    }

    /**
     * Add an average aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string|null  $column
     * @return $this
     */
    public function average($relationship = null, $column = null)
    {
        return $this->avg($relationship, $column);
    }

    /**
     * Add a sum aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string|null  $column
     * @return $this
     */
    public function sum($relationship = null, $column = null)
    {
        return $this->addAggregateRelationship($relationship, $column, 'sum');
    }

    /**
     * Add a maximum aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string|null  $column
     * @return $this
     */
    public function max($relationship = null, $column = null)
    {
        return $this->addAggregateRelationship($relationship, $column, 'max');
    }

    /**
     * Add a minimum aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string|null  $column
     * @return $this
     */
    public function min($relationship = null, $column = null)
    {
        return $this->addAggregateRelationship($relationship, $column, 'min');
    }

    /**
     * Get the sort instance as an array.
     *
     * @return array<string,mixed>|null
     */
    public function sortToArray()
    {
        $sort = $this->getSort();

        if (! $sort) {
            return null;
        }

        return [
            'active' => $sort->isActive(),
            'direction' => $sort->getDirection(),
            'next' => $sort->getNextDirection(),
        ];
    }

    /**
     * Get the column value for a record.
     *
     * @param  array<string, mixed>|Model  $value
     * @return array{mixed, bool}
     */
    public function value($value)
    {
        $this->record($value);

        if (! $this->getState()) {
            $this->state($this->getName());
        }

        return $this->apply($this->getResolvedState());
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string,mixed>
     */
    public function toArray()
    {
        return [
            'name' => $this->getParameter(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'hidden' => $this->isHidden(),
            'active' => $this->isActive(),
            'badge' => $this->isBadge(),
            'toggleable' => $this->isToggleable(),
            'class' => $this->getClasses(),
            'record_class' => $this->getRecordClasses(),
            'icon' => $this->getIcon(),
            'sort' => $this->sortToArray(),
        ];
    }

    /**
     * Define the column.
     *
     * @param  $this  $column
     * @return $this
     */
    protected function definition(self $column): self
    {
        return $column;
    }

    /**
     * Add a simple relationship to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string  $method
     * @return $this
     */
    protected function addSimpleRelationship($relationship, $method)
    {
        return $this->query(match (true) {
            (bool) $relationship => fn (Builder $query) => $query->{'with'.Str::studly($method)}($relationship),
            default => fn (Builder $query) => $query->{'with'.Str::studly($method)}(
                Str::beforeLast($this->getName(), '_'.$method),
            ),
        });
    }

    /**
     * Add an aggregate relationship to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @param  string|null  $column
     * @param  string  $method
     * @return $this
     */
    protected function addAggregateRelationship($relationship, $column, $method)
    {
        if ($relationship && ! $column) {
            throw new InvalidArgumentException(
                'A column must be specified when an aggregate relationship is used.'
            );
        }

        return $this->query(match (true) {
            (bool) $relationship => fn (Builder $query) => $query->{'with'.Str::studly($method)}($relationship, $column),
            default => fn (Builder $query) => $query->{'with'.Str::studly($method)}(
                Str::beforeLast($this->getName(), '_'.$method),
                Str::afterLast($this->getName(), $method.'_'),
            ),
        });
    }

    /**
     * Provide a selection of default dependencies for evaluation by name.
     *
     * @param  string  $parameterName
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName($parameterName)
    {
        return match ($parameterName) {
            'state' => [$this->getState()],
            'model', 'record', 'row' => [$this->getRecord()],
            default => parent::resolveDefaultClosureDependencyForEvaluationByName($parameterName),
        };
    }

    /**
     * Provide a selection of default dependencies for evaluation by type.
     *
     * @param  class-string  $parameterType
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType($parameterType)
    {
        $record = $this->getRecord();

        if (! $record instanceof Model) {
            return parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType);
        }

        return match ($parameterType) {
            Model::class, $record::class => [$record],
            default => parent::resolveDefaultClosureDependencyForEvaluationByType($parameterType),
        };
    }
}
