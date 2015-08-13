<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
class AndGroup extends Restriction implements AndGroupInterface
{
    /**
     * @return Restrictions[]
     */
    public function getValue() {
        return $this->value;
    }
}
