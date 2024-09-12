<?php

namespace Conquest\Table\Actions;

use Conquest\Core\Concerns\Routable;
use Conquest\Table\Actions\Enums\Context;

class PageAction extends BaseAction
{
    use Routable;

    public function setUp(): void
    {
        $this->setType(Context::Page->value);
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'route' => $this->getRoute(),
                'method' => $this->getMethod(),
            ]
        );
    }
}
