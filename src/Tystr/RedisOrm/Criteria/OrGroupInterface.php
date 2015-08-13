<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
interface OrGroupInterface
{
    /**
     * @return Restrictions[]
     */
    public function getValue();
}
