<?php

declare(strict_types=1);

namespace Honed\Table\Confirm;

use Honed\Core\Concerns\HasDescription;
use Honed\Core\Concerns\HasTitle;
use Honed\Core\Primitive;

class Confirm extends Primitive
{
    use Concerns\HasCancel;
    use Concerns\HasIntent;
    use Concerns\HasSuccess;
    use HasDescription;
    use HasTitle;

    /**
     * Create a confirm instance.
     *
     * @param  string|(\Closure(mixed...):string)|null  $title
     * @param  string|(\Closure(mixed...):string)|null  $description
     * @param  string|(\Closure(mixed...):string)|null  $cancel
     * @param  string|(\Closure(mixed...):string)|null  $success
     */
    public function __construct(
        string|\Closure|null $title = null,
        string|\Closure|null $description = null,
        string|\Closure|null $cancel = null,
        string|\Closure|null $success = null,
        ?string $intent = null,
    ) {
        parent::__construct();
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setCancel($cancel);
        $this->setSuccess($success);
        $this->setIntent($intent);
    }

    /**
     * Make a confirm instance.
     *
     * @param  string|(\Closure(mixed...):string)|null  $title
     * @param  string|(\Closure(mixed...):string)|null  $description
     * @param  string|(\Closure(mixed...):string)|null  $cancel
     * @param  string|(\Closure(mixed...):string)|null  $success
     */
    public static function make(
        string|\Closure|null $title = null,
        string|\Closure|null $description = null,
        string|\Closure|null $cancel = null,
        string|\Closure|null $success = null,
        ?string $intent = null,
    ): static {
        return resolve(static::class, compact('title', 'description', 'cancel', 'success', 'intent'));
    }

    public function toArray()
    {
        return [
            'title' => $this->getTitle(),
            'description' => $this->getDescription(),
            'cancel' => $this->getCancel(),
            'success' => $this->getSuccess(),
            'intent' => $this->getIntent(),
        ];
    }
}
