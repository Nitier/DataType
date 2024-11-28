<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent float in SQL
 */
class FloatType extends BaseType
{
    /**
     * The value of the float
     *
     * @var float|null
     */
    protected ?float $value;

    /**
     * The default value of the float
     *
     * @var float|null
     */
    protected ?float $defaultValue;

    /**
     * The length of the float
     *
     * @var int
     */
    protected int $length;

    /**
     * The number of decimal places
     *
     * @var int
     */
    protected int $decimalPlaces;

    /**
     * Whether the float is nullable or not
     *
     * @var bool
     */
    protected bool $isNullable;

    /**
     * Constructor
     *
     * @param int $length The length of the float
     * @param int $decimalPlaces The number of decimal places
     * @param float|null $defaultValue The default value of the float
     * @param bool $isNullable Whether the float is nullable or not
     * @param string $locale The locale to use for the translations
     */
    public function __construct(
        int $length = 10,
        int $decimalPlaces = 2,
        ?float $defaultValue = null,
        bool $isNullable = false,
        string $locale = 'en'
    ) {
        parent::__construct($locale);
        $this->length = $length;
        $this->decimalPlaces = $decimalPlaces;
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
        $this->value = $defaultValue;

        if ($defaultValue !== null) {
            $this->setValue($defaultValue);
        }
    }

    /**
     * Sets the value of the float
     *
     * @param mixed $value The value of the float
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

        if (!is_numeric($value)) {
            throw new \InvalidArgumentException($this->translate('VALUE_MUST_BE_FLOAT'));
        }

        $valueLength = mb_strlen(str_replace(['-', '.'], '', (string) $value));
        if ($valueLength > $this->length) {
            throw new \OverflowException($this->translate('VALUE_TOO_LONG', [
                'value' => $value,
                'length' => $this->length
            ]));
        }

        $this->value = round((float) $value, $this->decimalPlaces);
    }

    /**
     * Gets the value of the float
     *
     * @return float|null The value of the float
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * Gets the SQL declaration string for the float
     *
     * @return string The SQL declaration string
     */
    public function getSQLDeclaration(): string
    {
        $null = $this->isNullable ? 'NULL' : 'NOT NULL';
        $default = $this->defaultValue !== null ? "DEFAULT {$this->defaultValue}" : '';
        return sprintf("FLOAT(%d, %d) %s %s", $this->length, $this->decimalPlaces, $null, $default);
    }

    /**
     * Converts the float to an array
     *
     * @return array<string, mixed> The array representation of the float
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'length' => $this->length,
            'decimal_places' => $this->decimalPlaces,
            'nullable' => $this->isNullable,
            'default' => $this->defaultValue,
        ];
    }
}
