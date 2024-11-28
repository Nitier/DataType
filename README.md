# DataType Library

A PHP library for handling various data types with built-in validation for format, range, and nullability.

## 📦 Installation

Requires **PHP 8.0+**. Install the package via `Composer`:

```bash
composer require nitier/datatype
```

## 🛠 Supported Data Types

- **Text Types:** `TextType`, `VarcharType`, `TinyTextType`, `MediumTextType`, `LongTextType`
- **Temporal Types:** `DateType`, `TimeType`, `DatetimeType`, `TimestampType`, `YearType`
- **Numeric Types:** `IntType`, `FloatType`, `DecimalType`, `TinyIntType`, `MediumIntType`, `SmallIntType`

## 🚀 Usage Examples

### Text Types

#### `VarcharType`
```php
use Nitier\DataType\Type\VarcharType;

$varchar = new VarcharType(length: 100);
$varchar->setValue('Example text');
echo $varchar->getValue(); // Output: Example text

try {
    $varchar->setValue(str_repeat('A', 101)); 
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage(); // Error: length exceeded
}
```

### Temporal Types

#### `DateType`
```php
use Nitier\DataType\Type\DateType;

$date = new DateType();
$date->setValue('2023-11-28');
echo $date->getValue(); // Output: 2023-11-28

try {
    $date->setValue('28.11.2023');
} catch (\InvalidArgumentException $e) {
    echo $e->getMessage(); // Error: invalid format
}
```

## 📂 Project Structure

```
DataType/
├── src/
│   └── Type/
│       ├── TextType.php
│       ├── VarcharType.php
│       ├── DateType.php
│       ├── DatetimeType.php
│       └── ...
├── tests/
└── composer.json
```

## 🧪 Running Tests

Ensure **PHPUnit** is installed:

```bash
composer require --dev phpunit/phpunit
```

Run tests:

```bash
composer test
```

## 📝 License

Licensed under the **MIT License**. See `LICENSE` for details.

## 🤝 Contributions

Contributions are welcome via **pull requests** or **issues**. Let's make this library better together!
