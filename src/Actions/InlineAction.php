<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Table\Confirm\Concerns\Confirmable;
use Honed\Table\Confirm\Proxies\HigherOrderConfirm;
use Honed\Table\Url\Concerns\Urlable;
use Honed\Table\Url\Proxies\HigherOrderUrl;

/**
 * @property-read \Honed\Table\Confirm\Confirm $confirm
 * @property-read \Honed\Table\Url\Url $url
 */
class InlineAction extends BaseAction implements ProxiesHigherOrder
{
    use Concerns\Actionable;
    use Concerns\IsBulk;
    use Confirmable;
    use IsDefault;
    use Urlable;

    public function setUp(): void
    {
        $this->setType('inline');
    }

    /**
     * Dynamically forward calls to the proxies.
     *
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

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'hasAction' => $this->hasAction(),
            'confirm' => $this->getConfirm()?->toArray(),
            ...$this->isUrlable() ? [...$this->getUrl()?->toArray()] : [],
        ]);
    }
}
