<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Restrictions
{
    /**
     * @param string $key
     * @param int    $value
     * @return GreaterThan
     */
    public static function greaterThan($key, $value)
    {
        return new GreaterThan($key, (int) $value);
    }

    /**
     * @param string $key
     * @param int $value
     * @return LessThan
     */
    public static function lessThan($key, $value)
    {
        return new LessThan($key, (int) $value);
    }

    /**
     * @param string $key
     * @param int $value
     * @return EqualTo
     */
    public static function equalTo($key, $value)
    {
        return new EqualTo($key, $value);
    }
}
