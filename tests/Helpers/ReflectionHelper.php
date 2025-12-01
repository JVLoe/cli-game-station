<?php 

namespace Tests\Helpers;

use ReflectionClass;

trait ReflectionHelper
{
    private function setPrivateProperty($object, string $propertyName, $value): void
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    private function getPrivateProperty($object, string $propertyName)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }

    private function setSecretNumber($service, int $number): void
    {
        $this->setPrivateProperty($service, 'secretNumber', $number);
    }

}