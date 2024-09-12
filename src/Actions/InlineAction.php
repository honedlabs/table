<?php

declare(strict_types=1);

namespace Conquest\Table\Actions;

use Conquest\Core\Concerns\IsDefault;
use Conquest\Core\Concerns\Routable;
use Conquest\Core\Contracts\ProxiesHigherOrder;
use Conquest\Table\Actions\Concerns\Actionable;
use Conquest\Table\Actions\Concerns\CanBeConfirmable;
use Conquest\Table\Actions\Concerns\IsBulk;
use Conquest\Table\Actions\Confirm\Proxies\HigherOrderConfirm;
use Conquest\Table\Actions\Enums\Context;

/**
 * @property-read \Conquest\Table\Actions\Confirm\Confirm $confirm
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
     * @return \Conquest\Core\Contracts\HigherOrder
     *
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
