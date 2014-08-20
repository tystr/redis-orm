<?php

namespace Tystr\RedisOrm\Test\Model;

use Tystr\RedisOrm\Criteria\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class UserList
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var Criteria
     */
    protected $criteria;


    /**
     * @param string   $name
     * @param Criteria $criteria
     */
    public function __construct($name, Criteria $criteria)
    {
        $this->name = $name;
        $this->criteria = $criteria;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return Criteria
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param Criteria $criteria
     */
    public function setCriteria(Criteria $criteria)
    {
        $this->criteria = $criteria;
    }
}
