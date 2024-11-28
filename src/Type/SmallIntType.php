<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

/**
 * Class to represent small integers in SQL
 */
class SmallIntType extends IntType
{
    /**
     * The minimum value for a signed small integer
     */
    protected int $minValue = -32768;

    /**
     * The maximum value for a signed small integer
     */
    protected int $maxValue = 32767;

    /**
     * The maximum value for an unsigned small integer
     */
    protected int $unsignedMaxValue = 65535;

    /**
     * Constructor
     *
     * @param int $length The length of the integer
     * @param int|null $defaultValue The default value of the integer
     * @param bool $isUnsigned Whether the integer is unsigned or not
     * @param bool $isNullable Whether the integer is nullable or not
     * @param bool $autoIncrement Whether the integer should auto increment or not
     * @param bool $zeroFill Whether the integer should be zero filled or not
     * @param string $locale The locale to use for the translations
     */
    public function __construct(
        int $length = 5,
        ?int $defaultValue = null,
        bool $isUnsigned = false,
        bool $isNullable = false,
        bool $autoIncrement = false,
        bool $zeroFill = false,
        string $locale = 'en'
    ) {
        parent::__construct($length, $defaultValue, $isUnsigned, $isNullable, $autoIncrement, $zeroFill, $locale);
    }

    /**
     * Gets the SQL declaration string for the integer
     *
     * @return string The SQL declaration string
     */
    public function getSQLDeclaration(): string
    {
        return sprintf("SMALLINT(%d) %s", $this->length, $this->getAttributes());
    }
}
