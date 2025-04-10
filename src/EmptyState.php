<?php

declare(strict_types=1);

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
     * The label of the empty state action.
     *
     * @var string|null
     */
    protected $label;

    /**
     * The route action of the empty state.
     *
     * @var string|null
     */
    protected $action;

    /**
     * The message or state to display when the empty state is because of refiners.
     *
     * @var string|\Closure(\Honed\Table\EmptyState):void
     */
    protected $refining;

    /**
     * The message or state to display when the empty state is because of filters.
     *
     * @var string|\Closure(\Honed\Table\EmptyState):void
     */
    protected $filtering;

    /**
     * The message or state to display when the empty state is because of searching.
     *
     * @var string|\Closure(\Honed\Table\EmptyState):void
     */
    protected $searching;
    

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
     * Set the action of the empty state.
     *
     * @param  string  $label
     * @param  string|null $action
     * @return $this
     */
    public function action($label, $action = null)
    {
        $this->label = $label;
        $this->action = $action;

        return $this;
    }

    /**
     * Set the state to display when refining.
     * 
     * @param  string|\Closure(\Honed\Table\EmptyState):void  $refining
     * @return $this
     */
    public function whenRefining($refining)
    {
        $this->refining = $refining;

        return $this;
    }

    /**
     * Get the state to display when refining.
     *
     * @return string|\Closure(\Honed\Table\EmptyState):void
     */
    public function getRefiningState()
    {
        return $this->refining;
    }

    /**
     * Set the state to display when filtering.
     * 
     * @param  string|\Closure(\Honed\Table\EmptyState):void  $filtering
     * @return $this
     */
    public function whenFiltering($filtering)
    {
        $this->filtering = $filtering;

        return $this;
    }

    /**
     * Get the state to display when filtering.
     *
     * @return string|\Closure(\Honed\Table\EmptyState):void
     */
    public function getFilteringState()
    {
        return $this->filtering;
    }

    /**
     * Set the state to display when searching.
     * 
     * @param  string|\Closure(\Honed\Table\EmptyState):void  $searching
     * @return $this
     */
    public function whenSearching($searching)
    {
        $this->searching = $searching;

        return $this;
    }

    /**
     * Get the state to display when searching.
     *
     * @return string|\Closure(\Honed\Table\EmptyState):void
     */
    public function getSearchingState()
    {
        return $this->searching;
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
            'label' => $this->getLabel(),
            'action' => $this->getAction(),
        ];
    }    
}