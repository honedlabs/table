<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Action\Concerns\HasActions;
use Honed\Action\Concerns\HasParameterNames;
use Honed\Action\Handler;
use Honed\Action\Http\Requests\ActionRequest;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\RequiresKey;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
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
    use RequiresKey;

    /**
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
     * @return \Illuminate\Contracts\Support\Responsable|\Illuminate\Http\RedirectResponse|void
     */
    public function handle(ActionRequest $request)
    {
        $response = Handler::make(
            $this->getBuilder(),
            $this->getActions(),
            $this->getKeyName()
        )->handle($request);

        return $response;
    }

    /**
     * Get the key name for the table records.
     *
     * @throws \Honed\Core\Exceptions\MissingRequiredAttributeException
     */
    public function getKeyName(): string
    {
        try {
            return $this->getKey();
        } catch (MissingRequiredAttributeException $e) {
            return $this->getKeyColumn()?->getName() ?? throw $e;
        }
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
     *
     * @return array<string,string>
     */
    public function keysToArray(): array
    {
        return [
            'record' => $this->getKeyName(),
            'records' => $this->getRecordsKey(),
            'sorts' => $this->getSortKey(),
            'search' => $this->getSearchKey(),
            'columns' => $this->getColumnsKey(),
            ...($this->hasMatches() ? ['match' => $this->getMatchKey()] : []),
        ];
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
}
