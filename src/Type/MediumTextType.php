<?php

declare(strict_types=1);

namespace Nitier\DataType\Type;

/**
 * Class representing a MediumText type.
 */
class MediumTextType extends TextType
{
    public function __construct(
        bool $isNullable = false,
        ?string $defaultValue = null,
        string $encoding = 'UTF-8',
        string $locale = 'en'
    ) {
        parent::__construct(16777215, $isNullable, $defaultValue, $encoding, $locale);
    }
}
