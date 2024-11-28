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
     * Test setting and getting a value.
     */
    public function testSetValueAndGetValue(): void
    {
        $decimal = new DecimalType(10, 2);
        $decimal->setValue('1234.56');
        $this->assertSame('1234.56', $decimal->getValue());
    }

    /**
     * Test setting a nullable value.
     */
    public function testNullableValue(): void
    {
        $decimal = new DecimalType(10, 2, null, true);
        $decimal->setValue(null);
        $this->assertNull($decimal->getValue());
    }

    /**
     * Test throwing an exception when trying to set a nullable value with nullable=false.
     */
    public function testNullNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be NULL.');

        $decimal = new DecimalType(10, 2);
        $decimal->setValue(null);
    }

    /**
     * Test throwing an exception when trying to set an invalid value.
     */
    public function testInvalidValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a decimal number.');

        $decimal = new DecimalType(10, 2);
        $decimal->setValue('abc');
    }

    /**
     * Test throwing an exception when trying to set a value out of range.
     */
    public function testValueOutOfRange(): void
    {
        $this->expectException(\OverflowException::class);
        $this->expectExceptionMessage('Integer part exceeds the allowed length of 3.');

        $decimal = new DecimalType(5, 2); // Maximum 3 digits before the dot and 2 after
        $decimal->setValue('12345.67'); // Exceeds the range
    }

    /**
     * Test generating an SQL declaration.
     */
    public function testGetSQLDeclaration(): void
    {
        $decimal = new DecimalType(10, 2, '1000.50', false);
        $expectedSQL = "DECIMAL(10, 2) NOT NULL DEFAULT '1000.50'";
        $this->assertSame($expectedSQL, $decimal->getSQLDeclaration());
    }

    /**
     * Test generating an SQL declaration with a nullable value.
     */
    public function testGetSQLDeclarationWithNullable(): void
    {
        $decimal = new DecimalType(10, 2, null, true);
        $expectedSQL = "DECIMAL(10, 2) NULL";
        $this->assertSame($expectedSQL, $decimal->getSQLDeclaration());
    }

    /**
     * Test converting to an array.
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
