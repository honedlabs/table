<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

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
     * @var string|null
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
        return $this->disk ?? static::$useDisk;
    }

    /**
     * Set the default disk to retrieve images from.
     * 
     * @param  string|null  $disk
     * @return void
     */
    public static function useDisk($disk)
    {
        static::$useDisk = $disk;
    }
}
