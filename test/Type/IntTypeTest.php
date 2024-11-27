<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\IntType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(IntType::class)]
class IntTypeTest extends TestCase
{
    public function testSetValueNonInteger(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer.');

        $intType = new IntType(length: 5);
        $intType->setValue("123.45");  // Строка с дробным числом
    }

    public function testSetValueInvalidString(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be an integer.');

        $intType = new IntType(length: 5);
        $intType->setValue("invalid");  // Строка с недопустимыми символами
    }

    public function testSetValueValidInteger(): void
    {
        $intType = new IntType(length: 5);
        $intType->setValue(12345);

        $this->assertEquals(12345, $intType->getValue());
    }

    public function testSetValueStringInteger(): void
    {
        $intType = new IntType(length: 5);
        $intType->setValue("12345");

        $this->assertEquals(12345, $intType->getValue());
    }

    public function testSetValueTooLong(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value exceeds the allowed length of 5.');

        $intType = new IntType(length: 5);
        $intType->setValue(123456);
    }

    public function testSetValueUnsignedNegative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be negative for UNSIGNED.');

        $intType = new IntType(length: 5, isUnsigned: true);
        $intType->setValue(-1);
    }

    public function testSetValueNullWhenNotNullable(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be NULL.');

        $intType = new IntType(length: 5, isNullable: false);
        $intType->setValue(null);
    }

    public function testGetSQLDeclaration(): void
    {
        $intType = new IntType(length: 10, isUnsigned: true, autoIncrement: true, zeroFill: true, defaultValue: 1);

        $expectedSQL = "INT(10) UNSIGNED ZEROFILL AUTO_INCREMENT NOT NULL DEFAULT 1";
        $this->assertEquals($expectedSQL, $intType->getSQLDeclaration());
    }

    public function testIncrement(): void
    {
        $intType = new IntType(length: 5, autoIncrement: true);
        $intType->setValue(10);

        $intType->increment();

        $this->assertEquals(11, $intType->getValue());
    }

    public function testIncrementOverflow(): void
    {
        $this->expectException(\OverflowException::class);
        $this->expectExceptionMessage('Value exceeds the allowed length of 5.');

        $intType = new IntType(length: 5, autoIncrement: true);
        $intType->setValue(99999);

        $intType->increment(); // Это вызовет переполнение
    }

    public function testIncrementWithoutAutoIncrement(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Auto increment is not enabled for this field.');

        $intType = new IntType(length: 5);
        $intType->setValue(10);

        $intType->increment(); // Это вызовет исключение, так как автоинкремент не включен
    }

    public function testToArray(): void
    {
        $intType = new IntType(length: 10, isUnsigned: true, autoIncrement: true, zeroFill: true, defaultValue: 1);
        $intType->setValue(123);

        $expectedArray = [
            'value' => 123,
            'length' => 10,
            'unsigned' => true,
            'nullable' => false,
            'auto_increment' => true,
            'default' => 1,
            'zero_fill' => true,
        ];

        $this->assertEquals($expectedArray, $intType->toArray());
    }
}
