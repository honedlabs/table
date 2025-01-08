<?php

namespace Honed\Table\Exceptions;

use Exception;

class MissingResourceException extends Exception
{
    public function __construct(string $class)
    {
        parent::__construct(
            sprintf('[%s] requires a class-string, model or Eloquent resource to be supplied.', $class)
        );
    }
}
