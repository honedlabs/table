<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Honed\Core\Concerns\HasIcon;

class IconColumn extends Column
{
    use HasIcon;

    /**
     * {@inheritdoc}
     */
    protected $type = 'icon';

    /**
     * {@inheritdoc}
     *
     * @return \Closure(mixed):array{icon:mixed}
     */
    public function defineExtra()
    {
        return fn ($value) => [
            'icon' => $this->getIcon(),
        ];
    }
}
