<?php

declare(strict_types=1);

namespace Honed\Table\Exceptions;

use InvalidArgumentException;

class InvalidPaginatorException extends InvalidArgumentException
{
    /**
     * Create a new invalid paginator exception.
     *
     * @param  string  $paginator
     */
    public function __construct($paginator)
    {
        parent::__construct(
            \sprintf(
                'The provided paginator [%s] is invalid.',
                $paginator
            )
        );
    }

    /**
     * Throw a new invalid paginator exception.
     *
     * @param  string  $paginator
     * @return never
     *
     * @throws \Honed\Table\Exceptions\InvalidPaginatorException
     */
    public static function throw($paginator)
    {
        throw new self($paginator);
    }
}
