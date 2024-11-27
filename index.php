<?php
require 'vendor/autoload.php';
use Nitier\DataType\Type\IntType;

$val = new IntType(isNullable: true, isUnsigned: true);
$val->setValue(null);
var_dump($val->getValue());