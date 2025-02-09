<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\RequiresKey;
use Honed\Core\Exceptions\MissingRequiredAttributeException;
use Honed\Refine\Refine;

class Table extends Refine
{
    use Concerns\HasColumns;
    use Concerns\HasPages;
    use Concerns\HasRecords;
    use Concerns\HasResource;
    use Concerns\HasToggle;
    use Encodable;
    use RequiresKey;

    public static function make($modifier = null): static
    {
        return resolve(static::class)->modifier($modifier);
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
     * @return $this
     */
    public function buildTable(): static
    {
        if ($this->isRefined()) {
            return $this;
        }

        $resource = $this->getResource();

        $columns = $this->toggle();

        $this->modifyResource($resource);

        $this->refine();

        $records = $this->paginateRecords($resource);

        $formatted = $this->formatRecords($records, $columns, $this->getInlineActions(), $this->getSelector());

        $this->setRecords($formatted);

        return $this;
    }

    /**
     * @return array<string,mixed>
     */
    public function toArray(): array
    {
        $this->buildTable();

        return \array_merge(parent::toArray(), [
            'id' => $this->encodeClass(),
            'records' => $this->getRecords(),
            'meta' => $this->getMeta(),
            'columns' => $this->getColumns(),
            'pages' => $this->getPages(),
            'filters' => $this->getFilters(),
            'sorts' => $this->getSorts(),
            'endpoint' => $this->getEndpoint(),
            'toggleable' => $this->isToggleable(),
            'actions' => [
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
            ],
            'keys' => [
                'records' => $this->getKeyName(),
                'sorts' => $this->getSortKey(),
                'order' => $this->getOrderKey(),
                'search' => $this->getSearchKey(),
                'toggle' => $this->getToggleKey(),
                'pages' => $this->getPagesKey(),
                ...($this->hasMatches() ? ['match' => $this->getMatchKey()] : []),
            ],
        ]);
    }
}
