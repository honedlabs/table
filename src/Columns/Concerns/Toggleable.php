<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Table\Contracts\IsToggleable;

trait Toggleable
{
    /**
     * Whether the instance supports toggling.
     *
     * @var bool
     */
    protected $toggleable = true;

    /**
     * Whether the instance is toggled active by default.
     *
     * @var bool
     */
    protected $defaultToggled = false;

    /**
     * Whether the instance should always be shown, just hidden if it is inactive.
     *
     * @var bool
     */
    protected $always = false;

    /**
     * Set the instance to be toggleable.
     *
     * @param  bool  $value
     * @param  bool  $default
     * @return $this
     */
    public function toggleable($value = true, $default = false)
    {
        $this->toggleable = $value;
        $this->defaultToggled = $default;

        return $this;
    }

    /**
     * Determine if the instance is toggleable.
     *
     * @return bool
     */
    public function isToggleable()
    {
        return $this->toggleable || $this instanceof IsToggleable;
    }

    /**
     * Set the default toggled state.
     *
     * @param  bool  $default
     * @return $this
     */
    public function defaultToggled($default = true)
    {
        $this->defaultToggled = $default;

        return $this;
    }

    /**
     * Determine if the instance is toggled active by default.
     *
     * @return bool
     */
    public function isDefaultToggled()
    {
        return $this->defaultToggled;
    }

    /**
     * Set whether the instance should always be shown, just hidden if it is inactive.
     *
     * @param  bool  $always
     * @return $this
     */
    public function always($always = true)
    {
        $this->always = $always;

        return $this;
    }

    /**
     * Determine if the instance should always be shown, just hidden if it is inactive.
     *
     * @return bool
     */
    public function isAlways()
    {
        return $this->always;
    }
}
