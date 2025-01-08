<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Core\Link\Concerns\Linkable;
use Honed\Core\Link\Proxies\HigherOrderLink;

/**
 * @property-read \Honed\Core\Link\Link $link
 */
class PageAction extends BaseAction implements ProxiesHigherOrder
{
    use Linkable;

    public function setUp(): void
    {
        $this->setType('action:page');
    }

    public function __get(string $property): HigherOrder
    {
        return match ($property) {
            'link' => new HigherOrderLink($this),
            default => parent::__get($property),
        };
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(),
            $this->isLinkable() ? [...$this->getLink()?->toArray()] : [],
        );
    }
}
