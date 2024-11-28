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
     * Test setting and getting a valid string value.
     */
    public function testSetValueAndGetValue(): void
    {
        $varchar = new VarcharType(255);

        $varchar->setValue("Hello, world!");
        $this->assertSame("Hello, world!", $varchar->getValue());
    }

    /**
     * Test setting a value that exceeds the maximum length.
     */
    public function testSetValueTooLong(): void
    {
        $varchar = new VarcharType(10); // Limit length to 10 characters

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value exceeds the allowed length of 10.");

        $varchar->setValue("This is a very long string");
    }

    /**
     * Test setting a null value when the type is not nullable.
     */
    public function testSetValueNullNotNullable(): void
    {
        $varchar = new VarcharType(255, null); // Null is not allowed

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value cannot be NULL.");

        $varchar->setValue(null);
    }

    /**
     * Test setting a null value when the type is nullable.
     */
    public function testSetValueNullNullable(): void
    {
        $varchar = new VarcharType(255, null, true); // Null is allowed

        $varchar->setValue(null);
        $this->assertNull($varchar->getValue());
    }

    /**
     * Test setting a non-string value.
     */
    public function testSetValueNotString(): void
    {
        $varchar = new VarcharType(255);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Value must be a string.");

        $varchar->setValue(123); // Number instead of string
    }

    /**
     * Test the SQL declaration method.
     */
    public function testGetSQLDeclaration(): void
    {
        $varchar = new VarcharType(255, null, true);
        $sql = $varchar->getSQLDeclaration();

        $this->assertSame("VARCHAR(255)  NULL", $sql);
    }

    /**
     * Test conversion to an array.
     */
    public function testToArray(): void
    {
        $varchar = new VarcharType(255, null);
        $varchar->setValue("Test value");

        $expected = [
            'value' => "Test value",
            'length' => 255,
            'default' => null,
            'nullable' => false,
            'zero_fill' => false,
        ];

        $this->assertSame($expected, $varchar->toArray());
    }

    /**
     * Test value sanitization against potential XSS injection.
     */
    public function testInjectValue(): void
    {
        $varchar = new VarcharType(255, null);
        $varchar->setValue("<script>alert('xss');</script>");
        $this->assertSame("alert(&#039;xss&#039;);", $varchar->getValue());
        $this->assertSame("VARCHAR(255)  NOT NULL", $varchar->getSQLDeclaration());
        $this->assertSame([
            'value' => "alert(&#039;xss&#039;);",
            'length' => 255,
            'default' => null,
            'nullable' => false,
            'zero_fill' => false
        ], $varchar->toArray());
    }
}
