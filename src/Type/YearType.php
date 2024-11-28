<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a YEAR type with validation.
 */
class YearType extends BaseType
{
    private ?int $value;
    private bool $isNullable;
    private ?int $defaultValue;

    // Minimum and maximum valid year values.
    private const int MIN_YEAR = 1901;
    private const int MAX_YEAR = 2155;

    /**
     * Constructor
     *
     * @param bool $isNullable Whether the YEAR can be null
     * @param int|null $defaultValue Default year value (must be in 'YYYY' format)
     * @param string $locale Locale for translations
     */
    public function __construct(bool $isNullable = false, ?int $defaultValue = null, string $locale = 'en')
    {
        parent::__construct($locale);
        $this->isNullable = $isNullable;
        $this->defaultValue = $defaultValue;
        // Handle default value explicitly
        if ($defaultValue !== null) {
            $this->setValue($defaultValue);
        } else {
            $this->value = null;
        }
    }

    /**
     * Sets the value of the YEAR with range validation
     *
     * @param mixed $value
     * @throws \InvalidArgumentException
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

        if (!is_int($value)) {
            throw new \InvalidArgumentException($this->translate('VALUE_MUST_BE_INTEGER'));
        }

        if (!$this->isInRange($value)) {
            throw new \InvalidArgumentException($this->translate('YEAR_OUT_OF_RANGE', [
                'min' => self::MIN_YEAR, 'max' => self::MAX_YEAR
            ]));
        }

        $this->value = $value;
    }

    /**
     * Gets the value of the YEAR
     *
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Checks if the year is within the valid range
     *
     * @param int $year
     * @return bool
     */
    private function isInRange(int $year): bool
    {
        return $year >= self::MIN_YEAR && $year <= self::MAX_YEAR;
    }

    /**
     * Returns the SQL representation of the YEAR
     *
     * @return string
     */
    public function getSQLDeclaration(): string
    {
        return $this->isNullable ? 'YEAR NULL' : 'YEAR NOT NULL';
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'nullable' => $this->isNullable,
            'default' => $this->defaultValue,
        ];
    }
}
