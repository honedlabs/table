<?php

namespace Conquest\Table\Actions;

use Closure;
use Conquest\Core\Concerns\HasHttpMethod;
use Conquest\Core\Concerns\HasRoute;
use Conquest\Table\Table;

class PageAction extends BaseAction
{
    use HasHttpMethod;
    use HasRoute;

    public function setUp(): void
    {
        $this->setType(Table::PAGE_ACTION);
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(), 
            [
                'route' => $this->getResolvedRoute(),
                'method' => $this->getMethod(),
            ]
        );
    }
}
