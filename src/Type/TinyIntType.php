<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

/**
 * Class to represent tiny integers in SQL
 *
 * Tiny integers are 8-bit integers with a range of -128 to 127 (signed) or 0 to 255 (unsigned)
 */
class TinyIntType extends IntType
{
    protected int $minValue = -128;
    protected int $maxValue = 127;
    protected int $unsignedMaxValue = 255;

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
        int $length = 3,
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
     * Gets the SQL declaration string for the tiny integer
     *
     * @return string The SQL declaration string
     */
    public function getSQLDeclaration(): string
    {
        return sprintf("TINYINT(%d) %s", $this->length, $this->getAttributes());
    }
}
