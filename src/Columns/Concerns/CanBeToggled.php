<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

use Honed\Table\Contracts\IsToggleable;

trait CanBeToggled
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
    protected $toggledByDefault = false;

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
        $this->toggledByDefault = $default;

        return $this;
    }

    /**
     * Set the instance to not be toggleable.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notToggleable($value = true)
    {
        $this->toggleable = false;

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
     * Determine if the instance is not toggleable.
     *
     * @return bool
     */
    public function isNotToggleable()
    {
        return ! $this->isToggleable();
    }

    /**
     * Set the instance to be toggled active by default.
     *
     * @param  bool  $value
     * @return $this
     */
    public function toggledByDefault($value = true)
    {
        $this->toggledByDefault = $value;

        return $this;
    }

    /**
     * Set the instance to not be toggled active by default.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notToggledByDefault($value = true)
    {
        return $this->toggledByDefault(! $value);
    }

    /**
     * Determine if the instance is toggled active by default.
     *
     * @return bool
     */
    public function isToggledByDefault()
    {
        return $this->toggledByDefault;
    }

    /**
     * Determine if the instance is not toggled active by default.
     *
     * @return bool
     */
    public function isNotToggledByDefault()
    {
        return ! $this->isToggledByDefault();
    }

    /**
     * Set whether the instance should always be shown, just hidden if it is inactive.
     *
     * @param  bool  $value
     * @return $this
     */
    public function always($value = true)
    {
        $this->always = $value;

        return $this;
    }

    /**
     * Set the instance to not always be shown.
     *
     * @param  bool  $value
     * @return $this
     */
    public function notAlways($value = true)
    {
        return $this->always(! $value);
    }

    /**
     * Determine if the instance should always be shown, just hidden if it is inactive.
     */
    public function isAlways(): bool
    {
        return $this->always;
    }

    /**
     * Determine if the instance is not always shown.
     *
     * @return bool
     */
    public function isNotAlways()
    {
        return ! $this->isAlways();
    }
}
