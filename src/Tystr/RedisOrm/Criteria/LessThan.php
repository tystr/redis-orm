<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class LessThan extends Restriction implements LessThanInterface
{
    /**
     * @return int|string
     */
    public function getValue()
    {
        return (int) $this->value;
    }
}
