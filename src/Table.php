<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasParameterNames;
use Honed\Action\Handler;
use Honed\Action\Http\Requests\ActionRequest;
use Honed\Core\Concerns\Encodable;
use Honed\Refine\Refine;
use Honed\Table\Concerns\HasTableBindings;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Table extends Refine implements UrlRoutable
{
    use Concerns\HasColumns;
    use Concerns\HasEndpoint;
    use Concerns\HasModifier;
    use Concerns\HasRecords;
    use Concerns\HasResource;
    use Concerns\HasToggle;
    use Encodable;
    use HasActions;
    use HasParameterNames;
    use HasTableBindings;

    /**
     * A unique identifier column for the table.
     *
     * @var string|null
     */
    protected $key;

    /**
     * Get the unique identifier key for table records.
     *
     * @throws \RuntimeException When no key is defined
     */
    public function getKey(): string
    {
        $key = $this->key ?? $this->getKeyColumn()?->getName();

        if (\is_null($key)) {
            static::throwMissingKeyException();
        }

        return $key;
    }

    /**
     * Set the key property for the table.

     *
     * @return $this
     */
    public function key(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSortsKey(): string
    {
        if (isset($this->sortsKey)) {
            return $this->sortsKey;
        }

        return type(config('table.keys.sorts', 'sort'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchesKey(): string
    {
        if (isset($this->searchesKey)) {
            return $this->searchesKey;
        }

        return type(config('table.keys.searches', 'search'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public function getMatchesKey(): string
    {
        if (isset($this->matchesKey)) {
            return $this->matchesKey;
        }

        return type(config('table.keys.matches', 'match'))->asString();
    }

    /**
     * {@inheritdoc}
     */
    public function canMatch(): bool
    {
        if (isset($this->matches)) {
            return $this->matches;
        }

        return type(config('table.matches', false))->asBool();
    }

    /**
     * Create a new table instance.
     *
     * @param  \Closure|null  $modifier
     */
    public static function make($modifier = null): static
    {
        return resolve(static::class)
            ->modifier($modifier);
    }

    /**
     * Handle the incoming action request for this table.
     *
     * @return \Illuminate\Contracts\Support\Responsable|\Symfony\Component\HttpFoundation\RedirectResponse|void
     */
    public function handle(ActionRequest $request)
    {
        return Handler::make(
            $this->getBuilder(),
            $this->getActions(),
            $this->getKey()
        )->handle($request);
    }

    /**
     * @param  string  $name
     * @param  array<int, mixed>  $arguments
     */
    public function __call($name, $arguments): mixed
    {
        $args = Arr::first($arguments);

        match ($name) {
            'columns' => $this->addColumns($args), // @phpstan-ignore-line
            'sorts' => $this->addSorts($args), // @phpstan-ignore-line
            'filters' => $this->addFilters($args), // @phpstan-ignore-line
            'searches' => $this->addSearches($args), // @phpstan-ignore-line
            'actions' => $this->addActions($args), // @phpstan-ignore-line
            'pagination' => $this->pagination = $args, // @phpstan-ignore-line
            'paginator' => $this->paginator = $args, // @phpstan-ignore-line
            'endpoint' => $this->endpoint = $args, // @phpstan-ignore-line
            default => null,
        };

        return $this;
    }

    /**
     * Build the table using the given request.
     *
     * @return $this
     */
    public function buildTable(): static
    {
        if ($this->isRefined()) {
            return $this;
        }

        // Intermediate step allowing for table reuse with
        // minor changes between them.
        $this->evaluate($this->getModifier());

        // Execute the parent refine method to scope the builder
        // according to the given request.
        $this->refine();

        // If toggling is enabled, we need to determine which
        // columns are to be used from the request, cookie or by the
        // default state of each column.
        $activeColumns = $this->toggle($this->getColumns());

        // Retrieved the records, generate metadata and complete the
        // table pipeline.
        $this->formatAndPaginate($activeColumns);

        return $this;
    }

    public function getBuilder(): Builder
    {
        $this->builder ??= $this->createBuilder($this->getResource());

        return parent::getBuilder();
    }

    public function toArray(): array
    {
        $this->buildTable();

        return [
            'table' => $this->getRouteKey(),
            'records' => $this->getRecords(),
            'meta' => $this->getMeta(),
            'columns' => $this->getActiveColumns(),
            'pages' => $this->getPages(),
            'filters' => $this->getFilters(),
            'sorts' => $this->getSorts(),
            'toggle' => $this->canToggle(),
            'actions' => $this->actionsToArray(),
            'endpoint' => $this->getEndpoint(),
            'keys' => $this->keysToArray(),
        ];
    }

    /**
     * Get the keys for the table as an array.
     */
    public function keysToArray(): array
    {
        return \array_merge(parent::keysToArray(), [
            'record' => $this->getKey(),
            'records' => $this->getRecordsKey(),
            'columns' => $this->getColumnsKey(),
        ]);
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByName(string $parameterName): array
    {
        [$_, $singular, $plural] = $this->getParameterNames($this->getBuilder());

        return match ($parameterName) {
            'builder' => [$this->getBuilder()],
            'resource' => [$this->getResource()],
            'query' => [$this->getBuilder()],
            'request' => [$this->getRequest()],
            $singular => [$this->getBuilder()],
            $plural => [$this->getBuilder()],
            default => [],
        };
    }

    /**
     * @return array<mixed>
     */
    protected function resolveDefaultClosureDependencyForEvaluationByType(string $parameterType): array
    {
        [$model] = $this->getParameterNames($this->getBuilder());

        return match ($parameterType) {
            Builder::class => [$this->getBuilder()],
            Model::class => [$this->getBuilder()],
            Request::class => [$this->getRequest()],
            $model::class => [$this->getBuilder()],
            default => [],
        };
    }

    /**
     * Throw an exception if the table does not have a key column or key property defined.
     */
    protected static function throwMissingKeyException(): never
    {
        throw new \RuntimeException(
            'The table must have a key column or a key property defined.'
        );
    }
}
