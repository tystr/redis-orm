<?php

namespace Tystr\RedisOrm\KeyNamingStrategy;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ColonDelimitedKeyNamingStrategyTest extends \PHPUnit_Framework_TestCase
{
    public function testGetKeyName()
    {
        $parts = array('prefix', 'user', 123456);
        $strategy = new ColonDelimitedKeyNamingStrategy();

        assertEquals('prefix:user:123456', $strategy->getKeyName($parts));
    }
}