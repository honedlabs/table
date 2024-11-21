<?php

namespace Honed\Table\Actions;

use Honed\Table\Actions\Concerns\Actionable;
use Honed\Table\Actions\Concerns\CanBeConfirmable;
use Honed\Table\Actions\Concerns\Chunk\Chunks;
use Honed\Table\Actions\Concerns\IsDeselectable;
use Honed\Table\Actions\Concerns\IsInline;
use Honed\Table\Actions\Enums\Context;

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
