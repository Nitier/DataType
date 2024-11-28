<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

/**
 * Class representing a LongText type.
 */
class LongTextType extends TextType
{
    public function __construct(
        bool $isNullable = false,
        ?string $defaultValue = null,
        string $encoding = 'UTF-8',
        string $locale = 'en'
    ) {
        parent::__construct(PHP_INT_MAX, $isNullable, $defaultValue, $encoding, $locale);
    }
}
