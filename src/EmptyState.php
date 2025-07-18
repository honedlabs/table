<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Action\Operations\PageOperation;
use Honed\Core\Concerns\CanHaveIcon;
use Honed\Core\Contracts\NullsAsUndefined;
use Honed\Core\Primitive;
use Honed\Table\Concerns\AdaptsToRefinements;

/**
 * @extends Primitive<string, mixed>
 */
class EmptyState extends Primitive implements NullsAsUndefined
{
    use AdaptsToRefinements;
    use CanHaveIcon;

    public const DEFAULT_HEADING = 'No results found';

    public const DEFAULT_DESCRIPTION = 'There are no results to display.';

    /**
     * The identifier to use for evaluation.
     *
     * @var string
     */
    protected $evaluationIdentifier = 'emptyState';

    /**
     * The heading of the empty state.
     *
     * @var string
     */
    protected $heading = self::DEFAULT_HEADING;

    /**
     * The description of the empty state.
     *
     * @var string
     */
    protected $description = self::DEFAULT_DESCRIPTION;

    /**
     * The operations of the empty state.
     *
     * @var array<int, PageOperation>
     */
    protected $operations = [];

    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->define();
    }

    /**
     * Create a new empty state.
     */
    public static function make(?string $heading = null, ?string $description = null): static
    {
        return resolve(static::class)
            ->when($heading,
                fn ($emptyState, $heading) => $emptyState->heading($heading)
            )
            ->when($description,
                fn ($emptyState, $description) => $emptyState->description($description)
            );
    }

    /**
     * Set the heading of the empty state.
     *
     * @return $this
     */
    public function heading(string $heading): static
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get the heading of the empty state.
     */
    public function getHeading(): string
    {
        return $this->heading;
    }

    /**
     * Set the description of the empty state.
     *
     * @return $this
     */
    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the empty state.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets the operations of the empty state. This will replace any existing actions.
     *
     * @param  PageOperation|array<int, PageOperation>  $operations
     * @return $this
     */
    public function operations(PageOperation|array $operations): static
    {
        /** @var array<int, PageOperation> */
        $operations = is_array($operations) ? $operations : func_get_args();

        $this->operations = [...$this->operations, ...$operations];

        return $this;
    }

    /**
     * Add an operation to the empty state.
     *
     * @return $this
     */
    public function operation(PageOperation $operation): static
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Get the operations of the empty state.
     *
     * @return array<int, PageOperation>
     */
    public function getOperations(): array
    {
        return $this->operations;
    }

    /**
     * Get the representation of the instance.
     *
     * @return array<string, mixed>
     */
    protected function representation(): array
    {
        return [
            'heading' => $this->getHeading(),
            'description' => $this->getDescription(),
            'icon' => $this->getIcon(),
            'operations' => $this->operationsToArray(),
        ];
    }

    /**
     * Get the operations of the empty state as an array.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function operationsToArray(): array
    {
        return array_map(
            static fn (PageOperation $operation) => $operation->toArray(),
            array_values(
                array_filter(
                    $this->getOperations(),
                    static fn (PageOperation $operation) => $operation->isAllowed()
                )
            )
        );
    }
}
