<?php

namespace Conquest\Table\Actions;

use Conquest\Table\Actions\Concerns\Actionable;
use Conquest\Table\Actions\Concerns\CanBeConfirmable;
use Conquest\Table\Actions\Concerns\Chunk\Chunks;
use Conquest\Table\Actions\Concerns\IsDeselectable;
use Conquest\Table\Actions\Concerns\IsInline;
use Conquest\Table\Actions\Enums\Context;

class BulkAction extends BaseAction
{
    use Actionable;
    use CanBeConfirmable;
    use Chunks;
    use IsDeselectable;
    use IsInline;

    public function setUp(): void
    {
        $this->setType(Context::Bulk->value);
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'confirm' => $this->getConfirm()?->toArray(),
                'deselect' => $this->isDeselectable(),
            ]
        );
    }
}
