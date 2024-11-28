<?php

declare(strict_types=1);

namespace Nitier\DataType\Tests\Type;

use PHPUnit\Framework\TestCase;
use Nitier\DataType\Type\YearType;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(YearType::class)]
final class YearTypeTest extends TestCase
{
    public function testValidYear(): void
    {
        $year = new YearType();
        $year->setValue(2023);
        $this->assertSame(2023, $year->getValue());
    }

    public function testInvalidYearFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $year = new YearType();
        $year->setValue('202A'); // Некорректное значение
    }

    public function testYearOutOfRange(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $year = new YearType();
        $year->setValue(3000); // За пределами допустимого диапазона
    }

    public function testNullableYear(): void
    {
        $year = new YearType(isNullable: true);
        $year->setValue(null);
        $this->assertNull($year->getValue());
    }
}
