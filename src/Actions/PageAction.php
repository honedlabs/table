<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

class PageAction extends BaseAction
{
    use Concerns\Routable;

    public function setUp(): void
    {
        $this->setType('page');
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->getResolvedRoute(),
            'method' => $this->getMethod(),
        ]);
    }
}
