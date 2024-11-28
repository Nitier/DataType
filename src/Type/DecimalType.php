<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent decimal numbers in SQL.
 */
class DecimalType extends BaseType
{
    /**
     * The current value of the decimal type.
     *
     * @var string|null
     */
    protected ?string $value = null;

    /**
     * The precision of the decimal type.
     *
     * @var int
     */
    protected int $precision;

    /**
     * The scale of the decimal type.
     *
     * @var int
     */
    protected int $scale;

    /**
     * The default value of the decimal type.
     *
     * @var string|null
     */
    protected ?string $defaultValue;

    /**
     * Whether the decimal type is nullable.
     *
     * @var bool
     */
    protected bool $isNullable;

    /**
     * Constructor
     *
     * @param int $precision The precision of the decimal type
     * @param int $scale The scale of the decimal type
     * @param string|null $defaultValue The default value of the decimal type
     * @param bool $isNullable Whether the decimal type is nullable or not
     * @param string $locale The locale to use for the translations
     */
    public function __construct(
        int $precision = 10,
        int $scale = 2,
        ?string $defaultValue = null,
        bool $isNullable = false,
        string $locale = 'en'
    ) {
        parent::__construct($locale);
        $this->precision = $precision;
        $this->scale = $scale;
        $this->defaultValue = $defaultValue;
        $this->isNullable = $isNullable;

        if ($defaultValue !== null) {
            $this->setValue($defaultValue);
        }
    }

    /**
     * Sets the value of the decimal type.
     *
     * @param mixed $value The value to set
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
            throw new \InvalidArgumentException($this->translate('VALUE_MUST_BE_DECIMAL'));
        }

        $value = (string) $value;
        $parts = explode('.', $value);
        $integerPart = $parts[0];
        $decimalPart = $parts[1] ?? '';

        $integerMaxLength = $this->precision - $this->scale;

        // Проверка длины целой части
        if (mb_strlen($integerPart) > $integerMaxLength) {
            throw new \OverflowException($this->translate('INTEGER_PART_OUT_OF_RANGE', [
                'value' => $value,
                'length' => $integerMaxLength,
                'actual_length' => mb_strlen($integerPart),
            ]));
        }

        // Проверка длины дробной части
        if (mb_strlen($decimalPart) > $this->scale) {
            throw new \OverflowException($this->translate('DECIMAL_PART_OUT_OF_RANGE', [
                'value' => $value,
                'length' => $this->scale,
                'actual_scale' => mb_strlen($decimalPart),
            ]));
        }

        // Преобразуем в строку с фиксированной точностью
        $this->value = bcadd($value, '0', $this->scale);
    }

    /**
     * Gets the value of the decimal type.
     *
     * @return string|null The value of the decimal type
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Gets the SQL declaration string for the decimal type.
     *
     * @return string The SQL declaration string
     */
    public function getSQLDeclaration(): string
    {
        $null = $this->isNullable ? 'NULL' : 'NOT NULL';
        $default = $this->value !== null ? "DEFAULT '{$this->value}'" : '';
        return trim(sprintf("DECIMAL(%d, %d) %s %s", $this->precision, $this->scale, $null, $default));
    }

    /**
     * Converts the decimal type to an array.
     *
     * @return array<string, mixed> The array representation of the decimal type
     */
    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'precision' => $this->precision,
            'scale' => $this->scale,
            'nullable' => $this->isNullable,
            'default' => $this->defaultValue,
        ];
    }
}
