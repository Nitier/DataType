<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use Nitier\DataType\Type\TextType;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TextType::class)]
class TextTypeTest extends TestCase
{
    /**
     * Тест успешной установки и получения значения
     */
    public function testSetValueAndGetValue(): void
    {
        $text = new TextType();
        $text->setValue("This is a test text.");
        $this->assertSame("This is a test text.", $text->getValue());
    }

    /**
     * Тест установки значения `null` при условии, что тип не nullable
     */
    public function testSetValueNullNotNullable(): void
    {
        $text = new TextType(isNullable: false); // Не допускается null

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value cannot be NULL.");

        $text->setValue(null);
    }

    /**
     * Тест установки значения `null` при условии, что тип nullable
     */
    public function testSetValueNullNullable(): void
    {
        $text = new TextType(isNullable: true); // Допускается null

        $text->setValue(null);
        $this->assertNull($text->getValue());
    }

    /**
     * Тест установки значения не строкового типа
     */
    public function testSetValueNotString(): void
    {
        $text = new TextType();

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value must be a string.");

        $text->setValue(123); // Число вместо строки
    }

    /**
     * Тест получения SQL-декларации с null
     */
    public function testGetSQLDeclarationNullable(): void
    {
        $text = new TextType(isNullable: true, defaultValue: null);
        $this->assertSame("TEXT NULL", $text->getSQLDeclaration());
    }

    /**
     * Тест получения SQL-декларации без null и с default значением
     */
    public function testGetSQLDeclarationWithDefault(): void
    {
        $text = new TextType(isNullable: false, defaultValue: "Default text");
        $this->assertSame("TEXT NOT NULL DEFAULT 'Default text'", $text->getSQLDeclaration());
    }

    /**
     * Тест преобразования в массив
     */
    public function testToArray(): void
    {
        $text = new TextType(isNullable: true, defaultValue: "Sample text");
        $text->setValue("New value");

        $expected = [
            'value' => "New value",
            'nullable' => true,
            'default' => "Sample text",
            'encoding' => 'UTF-8',
            'maxLength' => 65535,
        ];

        $this->assertSame($expected, $text->toArray());
    }

    public function testInjectValue(): void
    {
        $text = new TextType();
        $text->setValue("<script>alert('xss');</script>");
        $this->assertSame("alert(&#039;xss&#039;);", $text->getValue());
        $this->assertSame("TEXT NOT NULL", $text->getSQLDeclaration());
        $this->assertSame([
            'value' => "alert(&#039;xss&#039;);",
            'nullable' => false,
            'default' => null,
            'encoding' => 'UTF-8',
            'maxLength' => 65535,
        ], $text->toArray());
    }
}
