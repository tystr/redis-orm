<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class LessThan extends Restriction
{
    /**
     * @return int|string
     */
    public function getValue()
    {
        return (int) $this->value;
    }
}
