<?php

namespace Honed\Table\Exceptions;

use Exception;

class InvalidPaginatorException extends Exception
{
    public function __construct(string $paginator)
    {
        parent::__construct(
            sprintf('[%s] is not a supported paginator type.', $paginator)
        );
    }
}
