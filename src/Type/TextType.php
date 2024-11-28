<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a TEXT type with additional features
 */
class TextType extends BaseType
{
    private ?string $value;       // The current value of the TEXT
    private bool $isNullable;     // Whether the TEXT can be null
    private ?string $defaultValue; // Default value (rarely used with TEXT)

    /**
     * Constructor
     *
     * @param bool $isNullable Whether the TEXT can be null
     * @param string|null $defaultValue The default value of the TEXT
     * @param string $locale The locale for translations
     */
    public function __construct(
        bool $isNullable = false,
        ?string $defaultValue = null,
        string $locale = 'en'
    ) {
        parent::__construct($locale);
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
        $this->value = $defaultValue;
    }

    /**
     * Sets the value of the TEXT
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

        // Sanitize input to prevent XSS
        if (is_string($value)) {
            $value = htmlspecialchars(strip_tags($value), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $this->value = $value;
    }

    /**
     * Gets the current value of the TEXT
     *
     * @return string|null The current value
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Returns the SQL declaration for the TEXT
     *
     * @return string The SQL declaration
     */
    public function getSQLDeclaration(): string
    {
        $null = $this->isNullable ? 'NULL' : 'NOT NULL';
        $default = $this->defaultValue !== null ? "DEFAULT '{$this->defaultValue}'" : '';

        // In most cases, TEXT types do not have default values, but support is here if needed.
        return rtrim(sprintf("TEXT %s %s", $null, $default));
    }

    /**
     * Converts the TEXT to an array
     *
     * @return array<string, mixed> The array representation
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'nullable' => $this->isNullable,
            'default' => $this->defaultValue,
        ];
    }
}
