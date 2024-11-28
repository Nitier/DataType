<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a TIME type with additional range validation.
 */
class TimeType extends BaseType
{
    private ?string $value;
    private bool $isNullable;
    private ?string $defaultValue;

    // Minimum and maximum valid time values.
    private const string MIN_TIME = '-838:59:59';
    private const string MAX_TIME = '838:59:59';

    /**
     * Constructor
     *
     * @param bool $isNullable Whether the TIME can be null
     * @param string|null $defaultValue Default time value (must be in 'HH:MM:SS' format)
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
     * Sets the value of the TIME with range validation
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
        if (!$this->isInRange($value)) {
            throw new \InvalidArgumentException($this->translate('TIME_OUT_OF_RANGE', [
                'min' => self::MIN_TIME,
                'max' => self::MAX_TIME
            ]));
        }
        if (!is_string($value) || !$this->isValidTime($value)) {
            throw new \InvalidArgumentException($this->translate('INVALID_TIME_FORMAT', ['format' => 'HH:MM:SS']));
        }



        $this->value = $value;
    }

    /**
     * Gets the value of the TIME
     *
     * @return string|null
     */
    public function getValue(): ?string
    {
        return $this->value;
    }

    /**
     * Validates the time format 'HH:MM:SS'
     *
     * @param string $time
     * @return bool
     */
    private function isValidTime(string $time): bool
    {
        // Using DateTime::createFromFormat for better validation
        $timeObject = \DateTime::createFromFormat('H:i:s', $time);
        return $timeObject !== false && $timeObject->format('H:i:s') === $time;
    }

    /**
     * Checks if the time is within the valid range
     *
     * @param string $time
     * @return bool
     */
    private function isInRange(string $time): bool
    {
        // Time format should be 'HH:MM:SS'
        return $time >= self::MIN_TIME && $time <= self::MAX_TIME;
    }

    /**
     * Returns the SQL representation of the TIME
     *
     * @return string
     */
    public function getSQLDeclaration(): string
    {
        return $this->isNullable ? 'TIME NULL' : 'TIME NOT NULL';
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
