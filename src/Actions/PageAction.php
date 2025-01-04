<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Table\Url\Concerns\Urlable;
use Honed\Table\Url\Proxies\HigherOrderUrl;

/**
 * @property-read \Honed\Table\Url\Url $url
 */
class PageAction extends BaseAction implements ProxiesHigherOrder
{
    use Urlable;

    public function setUp(): void
    {
        $this->setType('page');
    }

    /**
     * Dynamically forward calls to the proxies.
     *
     * @throws \Exception
     */
    public function __get(string $property): HigherOrder
    {
        return match ($property) {
            'url' => new HigherOrderUrl($this),
            default => throw new \Exception("Property [{$property}] does not exist on ".self::class),
        };
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(),
            $this->isUrlable() ? [...$this->getUrl()?->toArray()] : [],
        );
    }
}
