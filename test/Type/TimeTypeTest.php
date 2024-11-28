<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\TimeType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TimeType::class)]
class TimeTypeTest extends TestCase
{
    public function testValidTime(): void
    {
        $timeType = new TimeType(isNullable: false);
        $timeType->setValue('12:34:56'); // корректное время
        $this->assertEquals('12:34:56', $timeType->getValue());
    }

    public function testNullNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be NULL.');

        $timeType = new TimeType(isNullable: false);
        $timeType->setValue(null); // NULL не разрешен
    }

    public function testInvalidTimeFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid time format: HH:MM:SS');

        $timeType = new TimeType(isNullable: false);
        $timeType->setValue('25:00:00'); // Неверный формат времени
    }

    public function testTimeOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Time must be in the range of -838:59:59 to 838:59:59.');

        $timeType = new TimeType(isNullable: false);
        $timeType->setValue('838:59:60'); // Время вне диапазона
    }

    public function testDefaultTime(): void
    {
        $timeType = new TimeType(isNullable: true, defaultValue: '12:34:56');
        $this->assertEquals('12:34:56', $timeType->getValue()); // Значение по умолчанию
    }

    public function testNullableTime(): void
    {
        $timeType = new TimeType(isNullable: true);
        $timeType->setValue(null);
        $this->assertNull($timeType->getValue()); // Проверка на null
    }
}
