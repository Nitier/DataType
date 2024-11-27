<?php

declare(strict_types=1);

namespace Nitier\DataType\Abstract;

use Nitier\DataType\Interface\DataTypeInterface;

abstract class BaseType implements DataTypeInterface
{
    protected ?string $locale;
    /** @var array<string, string> */
    protected array $translations;

    public function __construct(string $locale = 'en')
    {
        $this->locale = $locale;
        $this->loadTranslations();
    }

    protected function loadTranslations(): void
    {
        $path = __DIR__ . "/../../lang/{$this->locale}.php";
        if (file_exists($path)) {
            $this->translations = include $path;
        } else {
            throw new \RuntimeException("Translation file for locale '{$this->locale}' not found.");
        }
    }
    /**
     * Translate message
     * @param string $key
     * @param array<string, mixed> $params
     * @return string
     */
    protected function translate(string $key, array $params = []): string
    {
        $message = $this->translations[$key] ?? $key;

        foreach ($params as $param => $value) {
            $message = mb_ereg_replace(
                "\{{$param}\}",
                mb_convert_encoding((string) $value, 'UTF-8', 'UTF-8'),
                $message
            );
        }

        return $message;
    }
}
