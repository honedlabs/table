<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

class BulkAction extends BaseAction
{
    // use Chunks;
    use Concerns\IsInline;
    use Concerns\Actionable;
    use Concerns\Confirmable;
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
