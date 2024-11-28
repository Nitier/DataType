<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a DATE type with additional range validation.
 */
class DateType extends BaseType
{
    private ?string $value;
    private bool $isNullable;
    private ?string $defaultValue;

    // Minimum and maximum valid date values.
    private const MIN_DATE = '1900-01-01';
    private const MAX_DATE = '9999-12-31';

    /**
     * Constructor
     *
     * @param bool $isNullable Whether the DATE can be null
     * @param string|null $defaultValue Default date value (must be in 'YYYY-MM-DD' format)
     * @param string $locale Locale for translations
     */
    public function __construct(bool $isNullable = false, ?string $defaultValue = null, string $locale = 'en')
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
     * Sets the value of the DATE with range validation
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

        if (!is_string($value) || !$this->isValidDate($value)) {
            throw new \InvalidArgumentException($this->translate('INVALID_DATE_FORMAT', ['format' => 'YYYY-MM-DD']));
        }

        if (!$this->isInRange($value)) {
            throw new \InvalidArgumentException($this->translate('DATE_OUT_OF_RANGE', [
                'min' => self::MIN_DATE, 'max' => self::MAX_DATE
            ]));
        }

        $this->value = $value;
    }

    /**
     * Gets the value of the DATE
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Validates the date format 'YYYY-MM-DD'
     *
     * @param string $date
     * @return bool
     */
    private function isValidDate(string $date): bool
    {
        // Using DateTime::createFromFormat for better validation
        $dateTime = \DateTime::createFromFormat('Y-m-d', $date);
        return $dateTime !== false && $dateTime->format('Y-m-d') === $date;
    }

    /**
     * Checks if the date is within the valid range
     *
     * @param string $date
     * @return bool
     */
    private function isInRange(string $date): bool
    {
        return $date >= self::MIN_DATE && $date <= self::MAX_DATE;
    }

    /**
     * Returns the SQL representation of the DATE
     *
     * @return string
     */
    public function getSQLDeclaration(): string
    {
        return $this->isNullable ? 'DATE NULL' : 'DATE NOT NULL';
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
