<?php

namespace App\Enum;

use ReflectionClass;

abstract class AbstractEnum
{
    /**
     * Get all class constants
     *
     * @return array
     */
    public static function getConstants(): array
    {
        $reflectionClass = new ReflectionClass(static::class);

        return $reflectionClass->getConstants();
    }
}
