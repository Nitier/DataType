<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\DateType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(DateType::class)]
class DateTypeTest extends TestCase
{
    public function testValidDate(): void
    {
        $dateType = new DateType(isNullable: false);
        $dateType->setValue('2024-11-28'); // корректная дата
        $this->assertEquals('2024-11-28', $dateType->getValue());
    }

    public function testNullNotAllowed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Value cannot be NULL.');

        $dateType = new DateType(isNullable: false);
        $dateType->setValue(null); // NULL не разрешен
    }

    public function testInvalidDateFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid date format: YYYY-MM-DD');

        $dateType = new DateType(isNullable: false);
        $dateType->setValue('2024-11-31'); // Неверный формат даты
    }

    public function testDateOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Date must be in the range of 1900-01-01 to 9999-12-31.');

        $dateType = new DateType(isNullable: false);
        $dateType->setValue('1800-01-01'); // Дата вне диапазона
    }

    public function testDefaultDate(): void
    {
        $dateType = new DateType(isNullable: true, defaultValue: '2024-11-28');
        $this->assertEquals('2024-11-28', $dateType->getValue()); // Значение по умолчанию
    }

    public function testNullableDate(): void
    {
        $dateType = new DateType(isNullable: true);
        $dateType->setValue(null);
        $this->assertNull($dateType->getValue()); // Проверка на null
    }
}
