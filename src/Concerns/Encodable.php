<?php

namespace Honed\Table\Concerns;

trait Encodable
{
    /**
     * @var (\Closure(string):string)|null
     */
    protected static $encoder = null;

    /**
     * @var (\Closure(string):string)|null
     */
    protected static $decoder = null;

    /**
     * Configure the encoding function to use for obfuscating values.
     * 
     * @param \Closure(string):string $encoder
     */
    public static function setEncoder(\Closure $encoder): void
    {
        static::$encoder = $encoder;
    }

    /**
     * Configure the decoding function to use for de-obfuscating values.
     * 
     * @param \Closure(string):string $decoder
     */
    public static function setDecoder(\Closure $decoder): void
    {
        static::$decoder = $decoder;
    }

    /**
     * Encode a value using the configured encoder.
     * 
     * @param string $value
     * @return string
     */
    public static function encodeValue(string $value): string
    {
        if (static::$encoder) {
            return value(static::$encoder, $value);
        }

        return encrypt($value);
    }

    /**
     * Decode a value using the configured decoder.
     * 
     * @param string $value
     * @return string
     */
    public static function decodeValue(string $value): string
    {
        if (static::$decoder) {
            return value(static::$decoder, $value);
        }

        return decrypt($value);
    }

    /**
     * Encode the current class name.
     * 
     * @return string
     */
    public function encodeClass(): string
    {
        return $this->encodeValue(static::class);
    }

    /**
     * Decode a class name.
     * 
     * @param string $value
     * @return string
     */
    public function decodeClass(string $value): string
    {
        return $this->decodeValue($value);
    }
}
