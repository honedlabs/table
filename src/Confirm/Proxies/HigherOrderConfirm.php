<?php

declare(strict_types=1);

namespace Honed\Table\Confirm\Proxies;

use Honed\Core\Contracts\HigherOrder;
use Honed\Core\Primitive;

/**
 * @internal
 *
 * @template T of \Honed\Core\Primitive
 *
 * @implements \Honed\Core\Contracts\HigherOrder<T>
 */
class HigherOrderConfirm implements HigherOrder
{
    /**
     * @param  T  $primitive
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
        $primitive = $this->primitive;

        $primitive->confirmInstance(); // @phpstan-ignore-line

        $confirm = $primitive->getConfirm(); // @phpstan-ignore-line
        if ($confirm && method_exists($confirm, $name)) {
            $confirm->{$name}(...$arguments);
        }

        return $primitive;
    }
}
