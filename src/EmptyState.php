<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Primitive;
use Honed\Core\Concerns\CanHaveIcon;
use Honed\Action\Operations\PageOperation;
use Honed\Core\Contracts\NullsAsUndefined;
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
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();

        $this->definition($this);
    }

    /**
     * Create a new empty state.
     *
     * @param  string|null  $heading
     * @param  string|null  $description
     * @return static
     */
    public static function make($heading = null, $description = null)
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
     * @param  string  $heading
     * @return $this
     */
    public function heading($heading)
    {
        $this->heading = $heading;

        return $this;
    }

    /**
     * Get the heading of the empty state.
     *
     * @return string
     */
    public function getHeading()
    {
        return $this->heading;
    }

    /**
     * Set the description of the empty state.
     *
     * @param  string  $description
     * @return $this
     */
    public function description($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description of the empty state.
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets the operations of the empty state. This will replace any existing actions.
     *
     * @param  PageOperation|array<int, PageOperation>  $operations
     * @return $this
     */
    public function operations($operations)
    {
        /** @var array<int, PageOperation> */
        $operations = is_array($operations) ? $operations : func_get_args();

        $this->operations = [...$this->operations, ...$operations];

        return $this;
    }

    /**
     * Add an operation to the empty state.
     *
     * @param  PageOperation  $operation
     * @return $this
     */
    public function operation($operation)
    {
        $this->operations[] = $operation;

        return $this;
    }

    /**
     * Get the operations of the empty state.
     *
     * @return array<int, PageOperation>
     */
    public function getOperations()
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
    protected function operationsToArray()
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

    /**
     * Define the empty state.
     *
     * @param  $this  $emptyState
     * @return $this
     */
    protected function definition(self $emptyState): self
    {
        return $emptyState;
    }
}
