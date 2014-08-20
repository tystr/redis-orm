<?php

namespace Tystr\RedisOrm\Criteria;

use Doctrine\Common\Collections\Collection;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
interface RestrictionInterface
{
    /**
     * @return string
     */
    public function getKey();

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @param mixed $value
     */
    public function setValue($value);

    /**
     * @param RestrictionInterface $restriction
     * @return bool
     */
    public function equals(RestrictionInterface $restriction);
}