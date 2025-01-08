<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Core\Link\Concerns\Linkable;
use Honed\Core\Link\Proxies\HigherOrderLink;
use Honed\Table\Confirm\Concerns\Confirmable;
use Honed\Table\Confirm\Proxies\HigherOrderConfirm;

/**
 * @property-read \Honed\Table\Confirm\Confirm $confirm
 * @property-read \Honed\Core\Link\Link $link
 */
class InlineAction extends BaseAction implements ProxiesHigherOrder
{
    use Concerns\Actionable;
    use Concerns\IsBulk;
    use Confirmable;
    use IsDefault;
    use Linkable;

    public function setUp(): void
    {
        $this->setType('action:inline');
    }

    public function __get(string $property): HigherOrder
    {
        return match ($property) {
            'confirm' => new HigherOrderConfirm($this),
            'link' => new HigherOrderLink($this),
            default => parent::__get($property),
        };
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            ...$this->isLinkable() ? [...$this->getLink()?->toArray()] : [],
        ]);
    }
}
