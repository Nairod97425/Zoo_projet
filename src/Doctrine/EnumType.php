<?php

namespace App\Doctrine;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class EnumType extends Type
{
    const ENUM = 'enum'; // Custom enum type name

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        // Define the SQL declaration for the enum type
        return "ENUM('value1', 'value2', 'value3')"; // Adjust the values as needed
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        // Convert database value to PHP value
        return (string) $value;
    }

    public function getName()
    {
        return self::ENUM;
    }
}
