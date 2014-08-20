<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
abstract class Restriction implements RestrictionInterface
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var string|int
     */
    protected $value;

    /**
     * @param string     $key
     * @param string|int $value
     */
    public function __construct($key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string|int|bool $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @param Restriction $restriction
     * @return bool
     */
    public function equals(RestrictionInterface $restriction)
    {
        return get_class($restriction) === get_class($this) &&
            $restriction->getKey() === $this->getKey() &&
            $restriction->getValue() === $this->getValue();
    }
}
