<?php

declare(strict_types=1);

namespace Honed\Table\Confirm;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasTitle;
use Honed\Core\Primitive;

class Confirm extends Primitive
{
    use HasTitle;
    use HasDescription;
    use Concerns\HasCancel;
    use Concerns\HasIntent;
    use Concerns\HasSubmit;

    /**
     * @param  array<string, array-key>  $state
     */
    public function __construct(array $state = [])
    {
        $this->setState($state);
    }

    /**
     * @return $this
     */
    public static function make(string|\Closure|null $title = null, string|\Closure|null $description = null): static
    {
        return resolve(static::class, compact('title', 'description'));
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'cancel' => $this->getCancel(),
            'submit' => $this->getSubmit(),
            'intent' => $this->getIntent(),
        ];
    }
}
