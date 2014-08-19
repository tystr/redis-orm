<?php

namespace Tystr\RedisOrm\DataTransformer;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
final class DataTypes
{
    const DATE = 'date';
    const STRING = 'string';
    const INTEGER = 'integer';
    const DOUBLE = 'double';
    const BOOLEAN = 'boolean';

    /**
     * @param string $dataType
     * @return bool
     */
    static public function isValidDataType($dataType)
    {
        $reflClass = new \ReflectionClass(new static());
        $constants = $reflClass->getConstants();

        return in_array($dataType, $constants);
    }

    private function __construct()
    {
    }
}
