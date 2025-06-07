<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use Closure;

class ImageColumn extends Column
{
    /**
     * {@inheritdoc}
     */
    protected $type = 'image';

    /**
     * The disk to retrieve the image from.
     *
     * @var string|null
     */
    protected $disk;

    /**
     * The default disk to retrieve the image from.
     *
     * @var string|\Closure(mixed...):string|null
     */
    protected static $useDisk;

    /**
     * Set the disk to retrieve the image from.
     *
     * @param  string|null  $disk
     * @return $this
     */
    public function disk($disk)
    {
        $this->disk = $disk;

        return $this;
    }

    /**
     * Get the disk to retrieve the image from.
     *
     * @return string|null
     */
    public function getDisk()
    {
        return $this->disk ??= $this->usesDisk();
    }

    /**
     * Set the default disk to retrieve images from.
     *
     * @param  string|\Closure(mixed...):string  $disk
     * @return void
     */
    public static function useDisk($disk = 'public')
    {
        static::$useDisk = $disk;
    }

    /**
     * Get the default disk to retrieve images from.
     *
     * @return string|null
     */
    protected function usesDisk()
    {
        if (is_null(static::$useDisk)) {
            return null;
        }

        if (static::$useDisk instanceof Closure) {
            static::$useDisk = $this->evaluate($this->useDisk);
        }

        return static::$useDisk;
    }
}
