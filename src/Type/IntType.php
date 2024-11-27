<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent integers in SQL
 */
class IntType extends BaseType
{
    /**
     * The current value of the integer type
     *
     * @var int|null
     */
    private ?int $value;

    /**
     * The length of the integer
     *
     * @var int
     */
    private int $length;

    /**
     * The default value of the integer
     *
     * @var int|null
     */
    private ?int $defaultValue;

    /**
     * Whether the integer is unsigned or not
     *
     * @var bool
     */
    private bool $isUnsigned;

    /**
     * Whether the integer is nullable or not
     *
     * @var bool
     */
    private bool $isNullable;

    /**
     * Whether the integer should auto increment or not
     *
     * @var bool
     */
    private bool $autoIncrement;

    /**
     * Whether the integer should be zero filled or not
     *
     * @var bool
     */
    private bool $zeroFill;

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
        int $length = 11,
        ?int $defaultValue = null,
        bool $isUnsigned = false,
        bool $isNullable = false,
        bool $autoIncrement = false,
        bool $zeroFill = false,
        string $locale = 'en'
    ) {
        parent::__construct($locale);
        $this->length = $length;
        $this->defaultValue = $defaultValue;
        $this->isUnsigned = $isUnsigned;
        $this->isNullable = $isNullable;
        $this->autoIncrement = $autoIncrement;
        $this->zeroFill = $zeroFill;
        $this->value = $defaultValue;
    }

    /**
     * Sets the value of the integer
     *
     * @param mixed $value The value of the integer
     *
     * @throws \InvalidArgumentException If the value is not an integer
     * @throws \OverflowException If the value is too big for the integer
     */
    public function setValue(mixed $value): void
    {
        // Check if the value is null
        if ($value === null && !$this->isNullable) {
            // Throw an exception if the value is null and the integer is not nullable
            throw new \InvalidArgumentException($this->translate('NULL_NOT_ALLOWED'));
        }

        // Check if the value is a string
        if (is_string($value)) {
            // Try to convert the string to an integer
            $convertedValue = intval($value);

            // Check if the string represents a valid integer (if conversion has changed the value)
            if ((string) $convertedValue !== trim($value)) {
                // Throw an exception if the value is not a valid integer representation
                throw new \InvalidArgumentException($this->translate("VALUE_MUST_BE_INTEGER"));
            }

            // If it's valid, assign the converted value
            $value = $convertedValue;
        }

        // Check if the value is not an integer
        if (!is_int($value) && $value !== null) {
            // Throw an exception if the value is not an integer
            throw new \InvalidArgumentException($this->translate("VALUE_MUST_BE_INTEGER"));
        }

        // Check if the integer is unsigned and the value is negative
        if ($this->isUnsigned && $value < 0) {
            // Throw an exception if the integer is unsigned and the value is negative
            throw new \InvalidArgumentException($this->translate('UNSIGNED_NEGATIVE'));
        }

        // Check if the value exceeds the valid range for INT
        if ($value !== null && !$this->isUnsigned && ($value < -2147483648 || $value > 2147483647)) {
            // Throw an exception if the value is out of range for a signed INT
            throw new \OverflowException($this->translate('VALUE_OUT_OF_RANGE', ['value' => $value, 'min' => -2147483648, 'max' => 2147483647]));
        }

        // Check if the value exceeds the valid range for UNSIGNED INT
        if ($this->isUnsigned && ($value < 0 || $value > 4294967295)) {
            // Throw an exception if the value is out of range for an unsigned INT
            throw new \OverflowException($this->translate('VALUE_OUT_OF_RANGE', ['value' => $value, 'min' => 0, 'max' => 4294967295]));
        }

        // Check if the value is too long for the integer
        $absoluteValueLength = mb_strlen(ltrim((string) $value, '-'));
        if ($absoluteValueLength > $this->length) {
            throw new \InvalidArgumentException(
                $this->translate('VALUE_TOO_LONG', ['value' => $value, 'length' => $this->length])
            );
        }

        // Set the value of the integer
        $this->value = $value;
    }


    /**
     * Gets the value of the integer
     *
     * @return int|null The value of the integer
     */
    public function getValue(): ?int
    {
        return $this->zeroFill ? str_pad((string) $this->value, $this->length, '0', STR_PAD_LEFT) : $this->value;
    }

    /**
     * Increments the value of the integer
     *
     * @throws \LogicException If auto increment is not enabled
     * @throws \OverflowException If the incremented value is too big for the integer
     */
    public function increment(): void
    {
        if (!$this->autoIncrement) {
            throw new \LogicException($this->translate('AUTO_INCREMENT_NOT_ENABLED'));
        }

        $newValue = $this->value + 1;

        if ($this->isUnsigned && $newValue < 0) {
            throw new \OverflowException($this->translate('UNSIGNED_OVERFLOW'));
        }

        if (mb_strlen((string) $newValue) > $this->length) {
            throw new \OverflowException($this->translate(
                'VALUE_TOO_LONG',
                ['value' => $newValue, 'length' => $this->length]
            ));
        }

        $this->value = $newValue;
    }

    /**
     * Gets the SQL declaration string for the integer
     *
     * @return string The SQL declaration string
     */
    public function getSQLDeclaration(): string
    {
        $attributes = [];
        if ($this->isUnsigned) {
            $attributes[] = 'UNSIGNED';
        }
        if ($this->zeroFill) {
            $attributes[] = 'ZEROFILL';
        }
        if ($this->autoIncrement) {
            $attributes[] = 'AUTO_INCREMENT';
        }

        $null = $this->isNullable ? 'NULL' : 'NOT NULL';
        $default = $this->defaultValue !== null ? "DEFAULT {$this->defaultValue}" : '';

        // Убираем возможные лишние пробелы в конце строки
        return rtrim(sprintf("INT(%d) %s %s %s", $this->length, implode(' ', $attributes), $null, $default));
    }

    /**
     * Converts the integer to an array
     *
     * @return array<string, mixed> The array representation of the integer
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'length' => $this->length,
            'unsigned' => $this->isUnsigned,
            'nullable' => $this->isNullable,
            'auto_increment' => $this->autoIncrement,
            'default' => $this->defaultValue,
            'zero_fill' => $this->zeroFill,
        ];
    }
}
