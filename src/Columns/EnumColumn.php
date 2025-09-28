<?php

declare(strict_types=1);

namespace Honed\Table\Columns;

use BackedEnum;
use RuntimeException;

class EnumColumn extends BadgeColumn
{
    /**
     * The backing enum for the column.
     *
     * @var class-string<BackedEnum>
     */
    protected $enum;

    /**
     * Provide the instance with any necessary setup.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->transformer(function (int|string|BackedEnum|null $value) {
            if ($this->missingEnum()) {
                throw new RuntimeException("Enum backing value is not set for {$this->getName()}.");
            }

            return match (true) {
                is_null($value) => null,
                $value instanceof BackedEnum => static::makeLabel($value->name),
                default => ($enum = $this->getEnum()::tryFrom($value)) ? static::makeLabel($enum->name) : null,
            };
        });
    }

    /**
     * Set the backing enum for the column.
     *
     * @param  class-string<BackedEnum>  $enum
     * @return $this
     */
    public function enum(string $enum): static
    {
        $this->enum = $enum;

        return $this;
    }

    /**
     * Get the backing enum for the column.
     *
     * @return class-string<BackedEnum>
     */
    public function getEnum(): string
    {
        return $this->enum;
    }

    /**
     * Check if the enum backing value is set.
     */
    public function hasEnum(): bool
    {
        return isset($this->enum);
    }

    /**
     * Check if the enum backing value is missing.
     */
    public function missingEnum(): bool
    {
        return ! $this->hasEnum();
    }
}
