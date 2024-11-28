<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\FloatType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(FloatType::class)]
class FloatTypeTest extends TestCase
{
    public function testCanSetAndGetValue(): void
    {
        $floatType = new FloatType(10, 2);
        $floatType->setValue(123.45);
        $this->assertSame(123.45, $floatType->getValue());
    }

    public function testDefaultValueIsSetCorrectly(): void
    {
        $floatType = new FloatType(10, 2, 45.67);
        $this->assertSame(45.67, $floatType->getValue());
    }

    public function testNullableAllowsNull(): void
    {
        $floatType = new FloatType(10, 2, null, true);
        $floatType->setValue(null);
        $this->assertNull($floatType->getValue());
    }

    public function testNonNullableThrowsExceptionOnNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be NULL.');
        $floatType = new FloatType(10, 2);
        $floatType->setValue(null);
    }

    public function testNonFloatValueThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value must be a float.');
        $floatType = new FloatType(10, 2);
        $floatType->setValue('not a float');
    }

    public function testValueTooLongThrowsException(): void
    {
        $this->expectException(\OverflowException::class);
        $this->expectExceptionMessage('Value exceeds the allowed length of 5.');
        $floatType = new FloatType(5, 2);
        $floatType->setValue(123456.78);
    }

    public function testGetSQLDeclaration(): void
    {
        $floatType = new FloatType(10, 2, 45.67, false);
        $expectedSQL = "FLOAT(10, 2) NOT NULL DEFAULT 45.67";
        $this->assertSame($expectedSQL, $floatType->getSQLDeclaration());
    }

    public function testToArray(): void
    {
        $floatType = new FloatType(10, 2, 45.67, true);
        $expectedArray = [
            'value' => 45.67,
            'length' => 10,
            'decimal_places' => 2,
            'nullable' => true,
            'default' => 45.67,
        ];
        $this->assertSame($expectedArray, $floatType->toArray());
    }
}
