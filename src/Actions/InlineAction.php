<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Concerns\IsDefault;


/**
 * @property-read \Honed\Table\Confirm\Confirm $confirm
 */
class InlineAction extends BaseAction
{
    use IsDefault;
    use Concerns\IsBulk;
    use Concerns\Actionable;
    use Concerns\Confirmable;
    use Concerns\Routable;

    public function setUp(): void
    {
        $this->setType('inline');
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'url' => $this->getResolvedRoute(),
            'method' => $this->getMethod(),
            'action' => $this->canAction(),
            'confirm' => $this->getConfirm()?->toArray(),
        ]);
    }

    // /**
    //  * Dynamically access the confirm property.
    //  * 
    //  * @param string $property
    //  * @return \Honed\Core\Contracts\HigherOrder
    //  * @throws \Exception
    //  */
    // public function __get(string $property)
    // {
    //     return match ($property) {
    //         'confirm' => new HigherOrderConfirm($this),
    //         default => throw new \Exception("Property [{$property}] does not exist on ".self::class),
    //     };
    // }
}
