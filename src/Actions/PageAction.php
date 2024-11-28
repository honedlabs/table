<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Contracts\HigherOrder;
use Honed\Table\Url\Concerns\Urlable;
use Honed\Table\Url\Proxies\HigherOrderUrl;
use Honed\Core\Contracts\ProxiesHigherOrder;

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

    public function toArray(): array
    {
        return array_merge(parent::toArray(), 
            $this->isUrlable() ? [...$this->getUrl()?->toArray()] : [],
        );
    }

    /**
     * Dynamically forward calls to the proxies.
     * 
     * @param string $property
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
}
