<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\DecimalType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DecimalType::class)]
class DecimalTypeTest extends TestCase
{
    /**
     * Тест на установку и получение значения.
     */
    public function testSetValueAndGetValue(): void
    {
        $decimal = new DecimalType(10, 2);
        $decimal->setValue('1234.56');
        $this->assertSame('1234.56', $decimal->getValue());
    }

    /**
     * Тест на установку значения NULL при nullable=true.
     */
    public function testNullableValue(): void
    {
        $decimal = new DecimalType(10, 2, null, true);
        $decimal->setValue(null);
        $this->assertNull($decimal->getValue());
    }

    /**
     * Тест на выброс исключения при попытке установить NULL, когда nullable=false.
     */
    public function testNullNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be NULL.');

        $decimal = new DecimalType(10, 2);
        $decimal->setValue(null);
    }

    /**
     * Тест на выброс исключения при попытке установить некорректное значение.
     */
    public function testInvalidValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a decimal number.');

        $decimal = new DecimalType(10, 2);
        $decimal->setValue('abc');
    }

    /**
     * Тест на выброс исключения при выходе за пределы precision и scale.
     */
    public function testValueOutOfRange(): void
    {
        $this->expectException(\OverflowException::class);
        $this->expectExceptionMessage('Value must be in the range of 2 to 3.');

        $decimal = new DecimalType(5, 2); // Максимум 3 знака перед точкой и 2 после
        $decimal->setValue('12345.67'); // Это выходит за пределы диапазона
    }

    /**
     * Тест на генерацию SQL-объявления.
     */
    public function testGetSQLDeclaration(): void
    {
        $decimal = new DecimalType(10, 2, '1000.50', false);
        $expectedSQL = "DECIMAL(10, 2) NOT NULL DEFAULT '1000.50'";
        $this->assertSame($expectedSQL, $decimal->getSQLDeclaration());
    }

    /**
     * Тест на генерацию SQL-объявления с NULL.
     */
    public function testGetSQLDeclarationWithNullable(): void
    {
        $decimal = new DecimalType(10, 2, null, true);
        $expectedSQL = "DECIMAL(10, 2) NULL";
        $this->assertSame($expectedSQL, $decimal->getSQLDeclaration());
    }

    /**
     * Тест на преобразование в массив.
     */
    public function testToArray(): void
    {
        $decimal = new DecimalType(10, 2, '123.45', false);
        $expectedArray = [
            'value' => '123.45',
            'precision' => 10,
            'scale' => 2,
            'nullable' => false,
            'default' => '123.45',
        ];
        $this->assertSame($expectedArray, $decimal->toArray());
    }
}
