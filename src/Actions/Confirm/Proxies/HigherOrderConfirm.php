<?php

namespace Honed\Table\Actions\Confirm\Proxies;

use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Primitive;

/**
 * @internal
 *
 * @mixin Honed\Table\Actions\Confirm\Confirmable
 *
 * @template T of Honed\Core\Primitive
 *
 * @template-implements Honed\Core\Concerns\HigherOrder
 */
class HigherOrderConfirm implements HigherOrder
{
    /**
     * @param T $primitive
     */
    public function __construct(
        protected readonly Primitive $primitive
    ) {}

    /**
     * Call the method on the confirm class
     * 
     * @return T
     */
    public function __call(string $name, array $arguments)
    {
        $this->primitive->setConfirm(true);

        $confirm = $this->primitive->getConfirm();
        if ($confirm && method_exists($confirm, $name)) {
            $confirm->{$name}(...$arguments);
        }

        return $this->primitive;
    }
}
