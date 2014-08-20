<?php

namespace Tystr\RedisOrm\Query;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ZRangeByScore
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int|string
     */
    protected $min;

    /**
     * @var int|string
     */
    protected $max;

    /**
     * @param string $key
     * @param string $min
     * @param string $max
     */
    public function __construct($key, $min = '-inf', $max = '+inf')
    {
        $this->key = $key;
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return int|string
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int|string $max
     */
    public function setMax($max)
    {
        $this->max = $max;
    }

    /**
     * @return int|string
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int|string $min
     */
    public function setMin($min)
    {
        $this->min = $min;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array($this->key, $this->min, $this->max);
    }
}
