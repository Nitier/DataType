<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a VARCHAR type with additional features
 */
class VarcharType extends BaseType
{
    private ?string $value;  // The current value of the VARCHAR
    private int $length;     // The maximum length of the VARCHAR
    private ?string $defaultValue; // The default value if not set
    private bool $isNullable;  // Whether the VARCHAR can be null
    private bool $zeroFill;    // Whether to zero fill the VARCHAR

    /**
     * Constructor
     *
     * @param int $length The maximum length of the VARCHAR
     * @param string|null $defaultValue The default value of the VARCHAR
     * @param bool $isNullable Whether the VARCHAR can be null
     * @param bool $zeroFill Whether to zero fill the VARCHAR
     * @param string $locale The locale for translations
     */
    public function __construct(
        int $length = 255,
        ?string $defaultValue = null,
        bool $isNullable = false,
        bool $zeroFill = false,
        string $locale = 'en'
    ) {
        parent::__construct($locale);
        $this->length = $length;
        $this->defaultValue = $defaultValue;
        $this->isNullable = $isNullable;
        $this->zeroFill = $zeroFill;
        $this->value = $defaultValue;
    }

    /**
     * Sets the value of the VARCHAR
     *
     * @param mixed $value The value to set
     *
     * @throws \InvalidArgumentException If the value is invalid
     */
    public function setValue(mixed $value): void
    {
        if ($value === null && !$this->isNullable) {
            throw new \InvalidArgumentException($this->translate('NULL_NOT_ALLOWED'));
        }

        if ($value !== null && !is_string($value)) {
            throw new \InvalidArgumentException($this->translate('VALUE_MUST_BE_STRING'));
        }

        if ($value !== null && mb_strlen($value) > $this->length) {
            throw new \InvalidArgumentException($this->translate(
                'VALUE_TOO_LONG',
                ['value' => $value, 'length' => $this->length]
            ));
        }

        // Sanitize the input to prevent XSS
        if (is_string($value)) {
            $value = htmlspecialchars(strip_tags($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        if ($value !== null && mb_strlen($value) > $this->length) {
            throw new \InvalidArgumentException(
                $this->translate('VALUE_TOO_LONG', ['value' => $value, 'length' => $this->length])
            );
        }

        $this->value = $value;
    }

    /**
     * Gets the current value of the VARCHAR
     *
     * @return string|null The current value
     */
    public function getValue(): ?string
    {
        $value = $this->value ?? '';
        if ($this->zeroFill && $this->value !== null) {
            return str_pad($value, $this->length, '0', STR_PAD_LEFT);
        }
        return $this->value;
    }

    /**
     * Returns the SQL declaration for the VARCHAR
     *
     * @return string The SQL declaration
     */
    public function getSQLDeclaration(): string
    {
        $attributes = [];
        if ($this->zeroFill) {
            $attributes[] = 'ZEROFILL'; // Zero fill, rarely used for VARCHAR
        }

        $default = $this->defaultValue !== null ? "DEFAULT '{$this->defaultValue}'" : '';
        $null = $this->isNullable ? 'NULL' : 'NOT NULL';

        return rtrim(sprintf("VARCHAR(%d) %s %s %s", $this->length, implode(' ', $attributes), $null, $default));
    }

    /**
     * Converts the VARCHAR to an array
     *
     * @return array<string, mixed> The array representation
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'length' => $this->length,
            'default' => $this->defaultValue,
            'nullable' => $this->isNullable,
            'zero_fill' => $this->zeroFill,
        ];
    }
}
