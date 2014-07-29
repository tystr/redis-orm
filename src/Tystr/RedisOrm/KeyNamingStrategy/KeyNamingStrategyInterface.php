<?php

namespace Tystr\RedisOrm\KeyNamingStrategy;

/**
 * @author Tyler Stroud <tyle@tylerstroud.com>
 */
interface KeyNamingStrategyInterface
{
    /**
     * @param array $parts
     * @return string
     */
    public function getKeyName(array $parts);
}
