<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

/**
 * Class to represent medium integers in SQL
 */
class MediumIntType extends IntType
{
    /**
     * The minimum value of the medium integer
     */
    protected int $minValue = -8388608;

    /**
     * The maximum value of the medium integer
     */
    protected int $maxValue = 8388607;

    /**
     * The maximum value of the unsigned medium integer
     */
    protected int $unsignedMaxValue = 16777215;

    /**
     * Constructor
     *
     * @param int $length The length of the medium integer
     * @param int|null $defaultValue The default value of the medium integer
     * @param bool $isUnsigned Whether the medium integer is unsigned or not
     * @param bool $isNullable Whether the medium integer is nullable or not
     * @param bool $autoIncrement Whether the medium integer should auto increment or not
     * @param bool $zeroFill Whether the medium integer should be zero filled or not
     * @param string $locale The locale to use for the translations
     */
    public function __construct(
        int $length = 8,
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
     * Gets the SQL declaration string for the medium integer
     *
     * @return string The SQL declaration string
     */
    public function getSQLDeclaration(): string
    {
        return sprintf("MEDIUMINT(%d) %s", $this->length, $this->getAttributes());
    }
}
