<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\DatetimeType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DatetimeType::class)]
class DatetimeTypeTest extends TestCase
{
    public function testValidDatetime(): void
    {
        $datetime = new DatetimeType();
        $datetime->setValue('2023-11-28 15:30:45');
        $this->assertSame('2023-11-28 15:30:45', $datetime->getValue());
    }

    public function testInvalidDatetimeFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $datetime = new DatetimeType();
        $datetime->setValue('2023-11-28');
    }

    public function testDatetimeOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $datetime = new DatetimeType();
        $datetime->setValue('9999-12-32 00:00:00');
    }

    public function testNullableDatetime(): void
    {
        $datetime = new DatetimeType(true);
        $datetime->setValue(null);
        $this->assertNull($datetime->getValue());
    }
}
