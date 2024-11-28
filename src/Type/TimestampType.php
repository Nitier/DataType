<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

/**
 * Class to represent a TIMESTAMP type with additional range validation.
 */
class TimestampType extends BaseType
{
    private ?int $value;
    private bool $isNullable;
    private ?int $defaultValue;

    // Minimum and maximum valid timestamp values.
    private const int MIN_TIMESTAMP = 0; // 1970-01-01 00:00:00 UTC
    private const int MAX_TIMESTAMP = 2147483647; // 2038-01-19 03:14:07 UTC

    /**
     * Constructor
     *
     * @param bool $isNullable Whether the TIMESTAMP can be null
     * @param int|null $defaultValue Default timestamp value (must be in UNIX timestamp format)
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
     * Sets the value of the TIMESTAMP with range validation
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
            throw new \InvalidArgumentException($this->translate('TIMESTAMP_OUT_OF_RANGE', [
                'min' => self::MIN_TIMESTAMP, 'max' => self::MAX_TIMESTAMP
            ]));
        }

        $this->value = $value;
    }

    /**
     * Gets the value of the TIMESTAMP
     *
     * @return int|null
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Checks if the timestamp is within the valid range
     *
     * @param int $timestamp
     * @return bool
     */
    private function isInRange(int $timestamp): bool
    {
        return $timestamp >= self::MIN_TIMESTAMP && $timestamp <= self::MAX_TIMESTAMP;
    }

    /**
     * Returns the SQL representation of the TIMESTAMP
     *
     * @return string
     */
    public function getSQLDeclaration(): string
    {
        return $this->isNullable ? 'TIMESTAMP NULL' : 'TIMESTAMP NOT NULL';
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
