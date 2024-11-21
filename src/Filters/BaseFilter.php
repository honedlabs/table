<?php

declare(strict_types=1);

namespace Honed\Table\Filters;

use Closure;
use Honed\Core\Concerns\CanTransform;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\HasValue;
use Honed\Core\Concerns\IsActive;
use Honed\Core\Concerns\IsAuthorized;
use Honed\Core\Concerns\Transforms;
use Honed\Core\Primitive;
use Honed\Table\Contracts\Filters;
use Illuminate\Support\Facades\Request;

abstract class BaseFilter extends Primitive implements Filters
{
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasType;
    use HasValue;
    use IsActive;
    use IsAuthorized;
    use Transforms;

    public function __construct(string|Closure $name, string|Closure|null $label = null)
    {
        parent::__construct();
        $this->setName($name);
        $this->setLabel($label ?? $this->toLabel($this->getName()));
    }

    /**
     * From the current request, get the value of the filter name
     * 
     * @return mixed
     */
    public function getValueFromRequest(): mixed
    {
        return Request::input($this->getName(), null);
    }

    /**
     * Determine if the filter should be applied.
     * 
     * @param mixed $value
     * @return bool
     */
    public function filtering(mixed $value): bool
    {
        return ! is_null($value);
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'active' => $this->isActive(),
            'value' => $this->getValue(),
            'meta' => $this->getMeta(),
        ];
    }
}
