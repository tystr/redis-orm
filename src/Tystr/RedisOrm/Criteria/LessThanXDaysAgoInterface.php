<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
interface LessThanXDaysAgoInterface
{
    /**
     * @return string
     */
    public function getValue();
}
