<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Closure;
use Honed\Core\Concerns\Allowable;
use Honed\Core\Concerns\CanBeActive;
use Honed\Core\Concerns\CanHaveAlias;
use Honed\Core\Concerns\CanHaveExtra;
use Honed\Core\Concerns\CanHaveIcon;
use Honed\Core\Concerns\CanQuery;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Primitive;
use Honed\Infolist\Entries\Concerns\CanBeAggregated;
use Honed\Infolist\Entries\Concerns\CanBeBadge;
use Honed\Infolist\Entries\Concerns\CanFormatValues;
use Honed\Infolist\Entries\Concerns\HasPlaceholder;
use Honed\Infolist\Entries\Concerns\HasState;
use Honed\Refine\Concerns\CanBeHidden;
use Honed\Refine\Concerns\HasQualifier;
use Honed\Refine\Sorts\Sort;
use Honed\Table\Columns\Concerns\CanBeKey;
use Honed\Table\Columns\Concerns\CanBeToggled;
use Honed\Table\Columns\Concerns\Exportable;
use Honed\Table\Columns\Concerns\Filterable;
use Honed\Table\Columns\Concerns\HasCellClasses;
use Honed\Table\Columns\Concerns\Searchable;
use Honed\Table\Columns\Concerns\Sortable;
use Honed\Table\Concerns\Selectable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 */
class Column extends Primitive implements NullsAsUndefined
{
    use Allowable;
    use CanBeActive;
    use CanBeAggregated;
    use CanBeBadge;
    use CanBeHidden;
    use CanBeKey;
    use CanBeToggled;
    use CanFormatValues;
    use CanHaveAlias;
    use CanHaveExtra;
    use CanHaveIcon;
    /** @use \Honed\Core\Concerns\CanQuery<TModel, TBuilder> */
    use CanQuery;
    use Exportable;
    use Filterable;
    use HasCellClasses;
    use HasLabel;
    use HasName;
    use HasPlaceholder;
    use HasQualifier;

    use HasState;

    use HasType;
    use Searchable;
    use Selectable;
    use Sortable;

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
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->active();
    }

    /**
     * Create a new column instance.
     */
    public static function make(string $name, ?string $label = null): static
    {
        return resolve(static::class)
            ->name($name)
            ->label($label ?? static::makeLabel($name));
    }

    /**
     * Get the parameter for the column.
     */
    public function getParameter(): string
    {
        return $this->getAlias()
            ?? str_replace('.', '_', $this->getName());
    }

    /**
     * Add a count of the related recordss the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function count(string|array|null $relationship = null): static
    {
        return $this->addSimpleRelationship($relationship, 'count');
    }

    /**
     * Add a relationship exists as the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function exists(string|array|null $relationship = null): static
    {
        return $this->addSimpleRelationship($relationship, 'exists');
    }

    /**
     * Add an average aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function avg(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->addAggregateRelationship($relationship, $column, 'avg');
    }

    /**
     * Add an average aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function average(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->avg($relationship, $column);
    }

    /**
     * Add a sum aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function sum(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->addAggregateRelationship($relationship, $column, 'sum');
    }

    /**
     * Add a maximum aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function max(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->addAggregateRelationship($relationship, $column, 'max');
    }

    /**
     * Add a minimum aggregate to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    public function min(string|array|null $relationship = null, ?string $column = null): static
    {
        return $this->addAggregateRelationship($relationship, $column, 'min');
    }

    /**
     * Get the sort instance as an array.
     *
     * @return array<string,mixed>|null
     */
    public function sortToArray(): ?array
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
     * @param  array<string, mixed>|Model|null  $value
     * @return array{mixed, bool}
     */
    public function value(array|Model|null $value): array
    {
        $this->record($value);

        if (! $this->getState()) {
            $this->state($this->getName());
        }

        return $this->apply($this->resolveState());
    }

    /**
     * Get the instance as an array.
     *
     * @return array<string,mixed>
     */
    protected function representation(): array
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
     * @return $this
     */
    protected function addSimpleRelationship(string|array|null $relationship, string $method): static
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
     * @return $this
     */
    protected function addAggregateRelationship(string|array|null $relationship, ?string $column, string $method): static
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
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
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
     * @return array<int, mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
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
