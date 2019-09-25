<?php

namespace App\Helper;


class ObjectAccessor
{
    public static function getPrivateProperty($object, string $propertyName)
    {
        $reflectionObject = new \ReflectionObject($object);
        $property = $reflectionObject->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($object);
    }
}