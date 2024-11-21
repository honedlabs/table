<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Concerns\IsDefault;
use Honed\Core\Concerns\Routable;
use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Contracts\ProxiesHigherOrder;
use Honed\Table\Actions\Concerns\Actionable;
use Honed\Table\Actions\Concerns\CanBeConfirmable;
use Honed\Table\Actions\Concerns\IsBulk;
use Honed\Table\Actions\Confirm\Proxies\HigherOrderConfirm;
use Honed\Table\Actions\Enums\Context;

/**
 * @property-read \Honed\Table\Actions\Confirm\Confirm $confirm
 */
class InlineAction extends BaseAction implements ProxiesHigherOrder
{
    use Actionable;
    use CanBeConfirmable;
    use IsBulk;
    use IsDefault;
    use Routable;

    public function setUp(): void
    {
        $this->setType(Context::Inline->value);
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'route' => $this->getRoute(),
                'method' => $this->getMethod(),
                'actionable' => $this->canAction(),
                'confirm' => $this->getConfirm()?->toArray(),
            ]
        );
    }

    /**
     * Dynamically access the confirm property.
     * 
     * @param string $property
     * @return \Honed\Core\Contracts\HigherOrder
     * @throws \Exception
     */
    public function __get(string $property)
    {
        return match ($property) {
            'confirm' => new HigherOrderConfirm($this),
            default => throw new \Exception("Property [{$property}] does not exist on ".self::class),
        };
    }
}
