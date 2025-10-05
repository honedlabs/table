<?php

declare(strict_types=1);

namespace Honed\Table\Concerns;

use Closure;
use Honed\Core\Concerns\CanBeActive;
use Honed\Core\Concerns\CanHaveIcon;
use Honed\Core\Concerns\CanQuery;
use Honed\Refine\Concerns\CanBeHidden;
use Honed\Refine\Concerns\HasQualifier;
use Honed\Table\Columns\Concerns\CanBeKey;
use Honed\Table\Columns\Concerns\CanBeToggled;
use Honed\Table\Columns\Concerns\Exportable;
use Honed\Table\Columns\Concerns\Filterable;
use Honed\Table\Columns\Concerns\HasAlignment;
use Honed\Table\Columns\Concerns\Searchable;
use Honed\Table\Columns\Concerns\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use InvalidArgumentException;

/**
 * @template TModel of \Illuminate\Database\Eloquent\Model = \Illuminate\Database\Eloquent\Model
 * @template TBuilder of \Illuminate\Database\Eloquent\Builder<TModel> = \Illuminate\Database\Eloquent\Builder<TModel>
 *
 * @phpstan-require-extends \Honed\Infolist\Entries\Entry<*, *>
 */
trait AsColumn
{
    use CanBeActive;
    use CanBeHidden;
    use CanBeKey;
    use CanBeToggled;
    use CanHaveIcon;

    /**
     * @use \Honed\Core\Concerns\CanQuery<TModel, TBuilder>
     */
    use CanQuery;

    use Exportable;
    use Filterable;
    use HasAlignment;
    use HasQualifier;
    use Searchable;
    use Selectable;
    use Selectable;
    use Sortable;

    /**
     * The classes to apply to the table header.
     *
     * @var array<int, string|Closure(mixed...):string>
     */
    protected $columnClasses = [];

    public function __construct()
    {
        parent::__construct();

        $this->define();
    }

    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->active();

        $this->selectable();
    }

    /**
     * Set the classes to apply to an individual cell.
     *
     * @param  string|Closure(mixed...):string  $classes
     * @return $this
     */
    public function columnClass(string|Closure $classes): static
    {
        $this->columnClasses[] = $classes;

        return $this;
    }

    /**
     * Get the classes to apply to an individual cell.
     *
     * @return string|null
     */
    public function getColumnClass()
    {
        return $this->createClasses($this->columnClasses);
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
     * Convert the class to an entry.
     *
     * @return array<string, mixed>
     */
    public function entry(): array
    {
        return $this->undefine(parent::representation());
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
            'class' => $this->getColumnClass(),
            'align' => $this->getAlignment(),
            'sort' => $this->sortToArray(),
        ];
    }

    /**
     * Add a simple relationship to the column state.
     *
     * @param  string|array<string, Closure>|null  $relationship
     * @return $this
     */
    protected function addSimpleRelationship(string|array|null $relationship, string $method): static
    {
        $this->notSelectable();

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
        $this->notSelectable();

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
}
