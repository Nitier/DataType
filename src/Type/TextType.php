<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a TEXT type with common features.
 */
class TextType extends BaseType
{
    protected ?string $value;
    protected bool $isNullable;
    protected ?string $defaultValue;
    protected string $encoding;
    protected int $maxLength;

    /**
     * Constructor
     *
     * @param int $maxLength The maximum length allowed for the TEXT
     * @param bool $isNullable Whether the TEXT can be null
     * @param string|null $defaultValue The default value of the TEXT
     * @param string $encoding The character encoding (e.g., 'UTF-8')
     * @param string $locale The locale for translations
     */
    public function __construct(
        int $maxLength = 65535,
        bool $isNullable = false,
        ?string $defaultValue = null,
        string $encoding = 'UTF-8',
        string $locale = 'en'
    ) {
        parent::__construct($locale);
        $this->maxLength = $maxLength;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
        $this->encoding = $encoding;
        $this->value = $defaultValue;
    }

    /**
     * Sets the value of the TEXT with encoding and length checks.
     *
     * @param mixed $value The value to set
     * @throws \InvalidArgumentException If the value is invalid, encoding doesn't match, or length exceeds limit
     */
    public function setValue(mixed $value): void
    {
        if ($value === null) {
            if (!$this->isNullable) {
                throw new \InvalidArgumentException($this->translate('NULL_NOT_ALLOWED'));
            }
            $this->value = null;
            return;
        }


        if (!is_string($value)) {
            throw new \InvalidArgumentException($this->translate('VALUE_MUST_BE_STRING'));
        }

        if (!mb_check_encoding($value, $this->encoding)) {
            throw new \InvalidArgumentException($this->translate(
                'INVALID_ENCODING',
                ['encoding' => $this->encoding]
            ));
        }

        if (mb_strlen($value, $this->encoding) > $this->maxLength) {
            throw new \InvalidArgumentException($this->translate(
                'VALUE_TOO_LONG',
                ['length' => $this->maxLength]
            ));
        }

        $value = htmlspecialchars(strip_tags($value), ENT_QUOTES | ENT_SUBSTITUTE, $this->encoding);

        $this->value = $value;
    }

    /**
     * Gets the current value of the TEXT.
     *
     * @return string|null The current value
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Returns the SQL declaration for the TEXT.
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
     * Converts the TEXT to an array.
     *
     * @return array<string, mixed> The array representation
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'nullable' => $this->isNullable,
            'default' => $this->defaultValue,
            'encoding' => $this->encoding,
            'maxLength' => $this->maxLength,
        ];
    }
}
