<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Table\Confirm\Concerns\Confirmable;

class BulkAction extends BaseAction
{
    use Concerns\Actionable;
    use Concerns\IsDeselecting;
    use Concerns\IsInline;
    use Confirmable;

    public function setUp(): void
    {
        $this->setType('action:bulk');
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            'deselect' => $this->isDeselecting(),
        ]);
    }
}
