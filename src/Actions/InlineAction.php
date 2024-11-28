<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Contracts\HigherOrder;
use Honed\Table\Url\Concerns\Urlable;
use Honed\Table\Url\Proxies\HigherOrderUrl;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Table\Confirm\Concerns\Confirmable;
use Honed\Table\Confirm\Proxies\HigherOrderConfirm;

/**
 * @property-read \Honed\Table\Confirm\Confirm $confirm
 * @property-read \Honed\Table\Url\Url $url
 */
class InlineAction extends BaseAction implements ProxiesHigherOrder
{
    use Urlable;
    use IsDefault;
    use Confirmable;
    use Concerns\IsBulk;
    use Concerns\Actionable;

    public function setUp(): void
    {
        $this->setType('inline');
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'action' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            ...$this->isUrlable() ? [...$this->getUrl()?->toArray()] : [],
        ]);
    }

    /**
     * Dynamically forward calls to the proxies.
     * 
     * @param string $property
     * @return \Honed\Core\Contracts\HigherOrder
     * @throws \Exception
     */
    public function __get(string $property): HigherOrder
    {
        return match ($property) {
            'confirm' => new HigherOrderConfirm($this),
            'url' => new HigherOrderUrl($this),
            default => throw new \Exception("Property [{$property}] does not exist on ".self::class),
        };
    }
}
