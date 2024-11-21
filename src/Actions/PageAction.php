<?php

namespace Honed\Table\Actions;

use Honed\Core\Concerns\Routable;
use Honed\Table\Actions\Enums\Context;

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
