<?php

namespace Honed\Table\Actions\Attributes;

use Attribute;
use Closure;
use Honed\Table\Actions\Confirm\Confirmable;

#[Attribute(Attribute::TARGET_CLASS)]
class Confirm
{
    /**
     * @var \Honed\Table\Actions\Confirm\Confirmable
     */
    protected $confirm;

    /**
     * Create a new confirmable instance.
     * 
     * @param string|\Closure $title
     * @param string|\Closure $description
     * @param string|\Honed\Table\Actions\Confirm\Enums\Intent|\Closure $intent
     * @param string|\Closure $cancel
     * @param string|\Closure $submit
     */
    public function __construct($title = null, $description = null, $intent = null, $cancel = null, $submit = null)
    {
        $this->confirm = new Confirmable(compact(
            'title',
            'description',
            'intent',
            'cancel',
            'submit',
        ));
    }

    /**
     * Get the confirm instance.
     * 
     * @return \Honed\Table\Actions\Confirm\Confirmable
     */
    public function getConfirm()
    {
        return $this->confirm;
    }
}
