<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\VarcharType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(VarcharType::class)]
class VarcharTypeTest extends TestCase
{
    /**
     * Тестирование успешной установки и получения значения
     */
    public function testSetValueAndGetValue(): void
    {
        $varchar = new VarCharType(255);

        $varchar->setValue("Hello, world!");
        $this->assertSame("Hello, world!", $varchar->getValue());
    }

    /**
     * Тестирование установления значения, которое превышает максимальную длину
     */
    public function testSetValueTooLong(): void
    {
        $varchar = new VarCharType(10); // Ограничение длины до 10 символов

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value exceeds the allowed length of 10.");

        $varchar->setValue("This is a very long string");
    }

    /**
     * Тестирование установки значения null при условии, что тип не nullable
     */
    public function testSetValueNullNotNullable(): void
    {
        $varchar = new VarCharType(255, false); // Не допускается null

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value cannot be NULL.");

        $varchar->setValue(null);
    }

    /**
     * Тестирование установки значения null при условии, что тип nullable
     */
    public function testSetValueNullNullable(): void
    {
        $varchar = new VarCharType(255, true); // Допускается null

        $varchar->setValue(null);
        $this->assertNull($varchar->getValue());
    }

    /**
     * Тестирование установки значения не строкового типа
     */
    public function testSetValueNotString(): void
    {
        $varchar = new VarCharType(255);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value must be a string.");

        $varchar->setValue(123); // Число вместо строки
    }

    /**
     * Тестирование метода получения SQL-декларации
     */
    public function testGetSQLDeclaration(): void
    {
        $varchar = new VarCharType(255, true);
        $sql = $varchar->getSQLDeclaration();

        $this->assertSame("VARCHAR(255) NULL", $sql);
    }

    /**
     * Тестирование преобразования в массив
     */
    public function testToArray(): void
    {
        $varchar = new VarCharType(255, false);
        $varchar->setValue("Test value");

        $expected = [
            'value' => "Test value",
            'length' => 255,
            'nullable' => false,
        ];

        $this->assertSame($expected, $varchar->toArray());
    }
}
