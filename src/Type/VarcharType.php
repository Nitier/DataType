<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

use Nitier\DataType\Abstract\BaseType;

class VarcharType extends BaseType
{
    private ?string $value;
    private int $length;
    private bool $isNullable;

    public function __construct(int $length = 255, bool $isNullable = false, string $locale = 'en')
    {
        parent::__construct($locale);
        $this->length = $length;
        $this->isNullable = $isNullable;
        $this->value = null;
    }

    public function setValue(mixed $value): void
    {
        if ($value === null && !$this->isNullable) {
            throw new \InvalidArgumentException($this->translate('NULL_NOT_ALLOWED'));
        }

        if ($value !== null && !is_string($value)) {
            throw new \InvalidArgumentException($this->translate('VALUE_MUST_BE_STRING'));
        }

        if ($value !== null && mb_strlen($value) > $this->length) {
            throw new \InvalidArgumentException($this->translate('VALUE_TOO_LONG', ['value' => $value, 'length' => $this->length]));
        }

        $this->value = $value;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function getSQLDeclaration(): string
    {
        return "VARCHAR({$this->length}) " . ($this->isNullable ? 'NULL' : 'NOT NULL');
    }

    public function toArray(): array
    {
        return [
            'value' => $this->value,
            'length' => $this->length,
            'nullable' => $this->isNullable,
        ];
    }
}


