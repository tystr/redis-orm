<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
interface AndGroupInterface
{
    /**
     * @return Restrictions[]
     */
    public function getValue();
}
