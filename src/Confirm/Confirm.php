<?php

declare(strict_types=1);

namespace Honed\Table\Confirm;

use Honed\Core\Primitive;
use Honed\Core\Concerns\HasTitle;
use Honed\Core\Concerns\HasDescription;

class Confirm extends Primitive
{
    use HasTitle;
    use HasDescription;
    use Concerns\HasCancel;
    use Concerns\HasIntent;
    use Concerns\HasSubmit;

    /**
     * Create a confirm instance.
     * 
     * @param string|(\Closure():string)|null $title
     * @param string|(\Closure():string)|null $description
     * @param string|(\Closure():string)|null $cancel
     * @param string|(\Closure():string)|null $submit
     * @param string|(\Closure():string)|null $intent
     */
    public function __construct(
        string|\Closure $title = null,
        string|\Closure $description = null,
        string|\Closure $cancel = null,
        string|\Closure $submit = null,
        string|\Closure $intent = null,
    ) {
        parent::__construct();
        $this->setTitle($title);
        $this->setDescription($description);
        $this->setCancel($cancel);
        $this->setSubmit($submit);
        $this->setIntent($intent);
    }

    /**
     * Make a confirm instance.
     * 
     * @param string|(\Closure():string)|null $title
     * @param string|(\Closure():string)|null $description
     * @param string|(\Closure():string)|null $cancel
     * @param string|(\Closure():string)|null $submit
     * @param string|(\Closure():string)|null $intent
     */
    public static function make(
        string|\Closure $title = null,
        string|\Closure $description = null,
        string|\Closure $cancel = null,
        string|\Closure $submit = null,
        string|\Closure $intent = null,
    ): static {
        return resolve(static::class, compact('title', 'description', 'cancel', 'submit', 'intent'));
    }

    // Needs a resolver

    public function toArray()
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
