<?php

declare(strict_types=1);

namespace Honed\Table\Actions;

use Honed\Core\Concerns\Authorizable;
use Honed\Core\Concerns\HasLabel;
use Honed\Core\Concerns\HasMeta;
use Honed\Core\Concerns\HasName;
use Honed\Core\Concerns\HasType;
use Honed\Core\Primitive;
use Honed\Table\Actions\Contracts\Action;

abstract class BaseAction extends Primitive implements Action
{
    use Authorizable;
    use HasLabel;
    use HasMeta;
    use HasName;
    use HasType;

    /**
     * Create a new action instance with a unique name, optionally specifying a display label.
     *
     * @param  string|(\Closure():string)|null  $label
     */
    final public function __construct(string $name, string|\Closure|null $label = null)
    {
        parent::__construct();
        $this->setName($name);
        $this->setLabel($label ?? $this->makeLabel($this->getName()));
    }

    /**
     * Make an action with a unique name, optionally the display label.
     *
     * @param  string|(\Closure():string)|null  $label
     * @return $this
     */
    final public static function make(string $name, string|\Closure|null $label = null): static
    {
        return resolve(static::class, compact('name', 'label'));
    }

    /**
     * Get the action as an array
     *
     * @return array<string,mixed>
     */
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
