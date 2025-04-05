<?php

namespace Honed\Table;

use Honed\Core\Concerns\HasIcon;
use Honed\Core\Primitive;

class EmptyState extends Primitive
{
    use HasIcon;

    /**
     * The title of the empty state.
     *
     * @var string|null
     */
    protected $title;

    /**
     * The message of the empty state.
     *
     * @var string|null
     */
    protected $message;

    /**
     * Create a new empty state.
     *
     * @param  string|null  $title
     * @param  string|null  $message
     * @return static
     */
    public static function make($title = null, $message = null)
    {
        return resolve(static::class)
            ->title($title)
            ->message($message);
    }

    /**
     * Set the title of the empty state.
     *
     * @param  string  $title
     * @return $this
     */
    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get the title of the empty state.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set the message of the empty state.
     *
     * @param  string  $message
     * @return $this
     */
    public function message($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get the message of the empty state.
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray()
    {
        return [
            'icon' => $this->getIcon(),
            'title' => $this->getTitle(),
            'message' => $this->getMessage(),
        ];
    }
}
