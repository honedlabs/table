<?php

declare(strict_types=1);

namespace Honed\Table\Columns\Concerns;

trait HasAlignment
{
    public const ALIGN_LEFT = 'left';

    public const ALIGN_CENTER = 'center';

    public const ALIGN_RIGHT = 'right';

    /**
     * The alignment of the column.
     *
     * @var 'left'|'center'|'right'
     */
    protected $alignment = self::ALIGN_LEFT;

    /**
     * Set the alignment of the column.
     *
     * @param  'left'|'center'|'right'  $alignment
     * @return $this
     */
    public function align(string $alignment): static
    {
        $this->alignment = $alignment;

        return $this;
    }

    /**
     * Set the alignment of the column.
     *
     * @param  'left'|'center'|'right'  $alignment
     * @return $this
     */
    public function alignment(string $alignment): static
    {
        return $this->align($alignment);
    }

    /**
     * Set the alignment of the column to left.
     *
     * @return $this
     */
    public function alignLeft(): static
    {
        return $this->align(self::ALIGN_LEFT);
    }

    /**
     * Set the alignment of the column to center.
     *
     * @return $this
     */
    public function alignCenter(): static
    {
        return $this->align(self::ALIGN_CENTER);
    }

    /**
     * Set the alignment of the column to right.
     *
     * @return $this
     */
    public function alignRight(): static
    {
        return $this->align(self::ALIGN_RIGHT);
    }

    /**
     * Get the alignment of the column.
     *
     * @return 'left'|'center'|'right'
     */
    public function getAlignment(): string
    {
        return $this->alignment;
    }
}
