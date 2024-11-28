<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\TimestampType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TimestampType::class)]
final class TimestampTypeTest extends TestCase
{
    public function testValidTimestamp(): void
    {
        $timestamp = new TimestampType();
        $timestamp->setValue(1672531200); // 2023-01-01 00:00:00 UTC
        $this->assertSame(1672531200, $timestamp->getValue());
    }

    public function testInvalidTimestampFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $timestamp = new TimestampType();
        $timestamp->setValue('invalid');
    }

    public function testTimestampOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $timestamp = new TimestampType();
        $timestamp->setValue(2147483648); // за пределами допустимого диапазона
    }

    public function testNullableTimestamp(): void
    {
        $timestamp = new TimestampType(true);
        $timestamp->setValue(null);
        $this->assertNull($timestamp->getValue());
    }
}
