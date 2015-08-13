<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
class OrGroup extends Restriction implements OrGroupInterface
{
    /**
     * @return Restrictions[]
     */
    public function getValue() {
        return $this->value;
    }
}
