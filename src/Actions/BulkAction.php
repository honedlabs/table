<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Table\Confirm\Concerns\Confirmable;

class BulkAction extends BaseAction
{
    use Concerns\IsInline;
    use Concerns\Actionable;
    use Confirmable;
    use Concerns\DeselectsOnEnd;

    public function setUp(): void
    {
        $this->setType('bulk');
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'confirm' => $this->getConfirm()?->toArray(),
            'deselect' => $this->deselectsOnEnd(),
        ]);
    }
}
