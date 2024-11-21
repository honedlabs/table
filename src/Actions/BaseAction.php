<?php

namespace Honed\Table\Actions;

use Closure;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Concerns\IsAuthorized;
use Honed\Core\Primitive;

abstract class BaseAction extends Primitive
{
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasType;
    use IsAuthorized;

    public function __construct(string $label, string|Closure|null $name = null)
    {
        parent::__construct();
        $this->setLabel($label);
        $this->setName($name ?? $this->toName($label));
    }
    
    public static function make(string $label, string|Closure $name = null): static
    {
        return resolve(static::class, compact('label', 'name'));
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'label' => $this->getLabel(),
            'type' => $this->getType(),
            'meta' => $this->getMeta(),
        ];
    }
}
