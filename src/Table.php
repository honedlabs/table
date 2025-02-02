<?php

declare(strict_types=1);

namespace Honed\Table;

use Honed\Refine\Refine;
use Honed\Core\Concerns\Encodable;
use Honed\Core\Concerns\RequiresKey;
use Honed\Core\Exceptions\MissingRequiredAttributeException;

class Table extends Refine
{
    use Concerns\HasRecords;
    use Concerns\HasPages;
    use Concerns\HasColumns;
    use Concerns\HasResource;
    use Concerns\Toggleable;
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

        $columns = $this->getColumns();
        
        $this->modifyResource($resource);

        $this->refine();
        
        $records = $this->paginateRecords($resource);
        $formatted = $this->formatRecords($records, $columns, $this->getInlineActions(), $this->getSelector());
        $this->setRecords($formatted);

        return $this;
    }

    public function toArray(): array
    {
        $this->buildTable();

        return [
            'id' => $this->encodeClass(),
            'endpoint' => $this->isAnonymous() ? null : $this->getEndpoint(),
            'toggleable' => $this->isToggleable(),
            'keys' => [
                'records' => $this->getKeyName(),
                'sorts' => $this->getSortKey(),
                'order' => $this->getOrderKey(),
                'search' => $this->getSearchKey(),
                'toggle' => $this->getToggleKey(),
                'pages' => $this->getPagesKey(),
                ...($this->hasMatches() ? ['match' => $this->getMatchKey()] : []),

            ],
            'records' => $this->getRecords(),
            'columns' => $this->getColumns(),
            'actions' => [
                'bulk' => $this->getBulkActions(),
                'page' => $this->getPageActions(),
            ],
            'filters' => $this->getFilters(),
            'sorts' => $this->getSorts(),
            'pages' => $this->getPages(),
        ];
    }
}
