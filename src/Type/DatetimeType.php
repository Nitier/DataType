<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a DATETIME type with additional range validation.
 */
class DatetimeType extends BaseType
{
    private ?string $value;
    private bool $isNullable;
    private ?string $defaultValue;

    // Minimum and maximum valid datetime values.
    private const string MIN_DATETIME = '1970-01-01 00:00:00';
    private const string MAX_DATETIME = '9999-12-31 23:59:59';

    /**
     * Constructor
     *
     * @param bool $isNullable Whether the DATETIME can be null
     * @param string|null $defaultValue Default datetime value (must be in 'YYYY-MM-DD HH:MM:SS' format)
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
     * Sets the value of the DATETIME with range validation
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

        if (!is_string($value) || !$this->isValidDatetime($value)) {
            throw new \InvalidArgumentException($this->translate(
                'INVALID_DATETIME_FORMAT',
                ['format' => 'YYYY-MM-DD HH:MM:SS']
            ));
        }

        if (!$this->isInRange($value)) {
            throw new \InvalidArgumentException($this->translate('DATETIME_OUT_OF_RANGE', [
                'min' => self::MIN_DATETIME,
                'max' => self::MAX_DATETIME
            ]));
        }

        $this->value = $value;
    }

    /**
     * Gets the value of the DATETIME
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Validates the datetime format 'YYYY-MM-DD HH:MM:SS'
     *
     * @param string $datetime
     * @return bool
     */
    private function isValidDatetime(string $datetime): bool
    {
        // Using DateTime::createFromFormat for better validation
        $datetimeObject = \DateTime::createFromFormat('Y-m-d H:i:s', $datetime);
        return $datetimeObject !== false && $datetimeObject->format('Y-m-d H:i:s') === $datetime;
    }

    /**
     * Checks if the datetime is within the valid range
     *
     * @param string $datetime
     * @return bool
     */
    private function isInRange(string $datetime): bool
    {
        return $datetime >= self::MIN_DATETIME && $datetime <= self::MAX_DATETIME;
    }

    /**
     * Returns the SQL representation of the DATETIME
     *
     * @return string
     */
    public function getSQLDeclaration(): string
    {
        return $this->isNullable ? 'DATETIME NULL' : 'DATETIME NOT NULL';
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
