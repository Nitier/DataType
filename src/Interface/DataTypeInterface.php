<?php

declare(strict_types=1);

namespace Nitier\DataType\Interface;

/**
 * Interface for data types.
 * Contains a set of methods that must be implemented for each data type.
 */
interface DataTypeInterface
{
    /**
     * Sets the value of the data type.
     *
     * @param mixed $value
     */
    public function setValue(mixed $value): void;

    /**
     * Returns the value of the data type.
     *
     * @return mixed
     */
    public function getValue(): mixed;

    /**
     * Returns the SQL declaration string for the data type.
     *
     * @return string
     */
    public function getSQLDeclaration(): string;

    /**
     * Returns an array representation of the data type.
     * @return array<string, mixed>
     */
    public function toArray(): array;
}
