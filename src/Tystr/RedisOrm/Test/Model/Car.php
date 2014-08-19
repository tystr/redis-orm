<?php

namespace Tystr\RedisOrm\Test\Model;

use DateTime;
use Tystr\RedisOrm\Annotations\Date;
use Tystr\RedisOrm\Annotations\Field;
use Tystr\RedisOrm\Annotations\Index;
use Tystr\RedisOrm\Annotations\Id;
use Tystr\RedisOrm\Annotations\Prefix;
use Tystr\RedisOrm\Annotations\Type;

/**
 * @Prefix("cars")
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Car
{
    /**
     * @var string
     * @Field(type="integer")
     * @Id
     */
    protected $id;

    /**
     * @var string
     * @Field(type="string")
     * @Index
     */
    protected $make;

    /**
     * @var string
     * @Field(type="string")
     * @Index
     */
    protected $model;

    /**
     * @var string
     * @Field(type="string")
     * @Index(name="engine_type")
     */
    protected $engineType;

    /**
     * @var string
     * @Field(type="string")
     * @Index
     */
    protected $color;

    /**
     * @var DateTime
     * @Field(type="date", name="manufacture_date")
     * @Date(name="manufacture_date")
     */
    protected $manufactureDate;

    /**
     * @return string
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    /**
     * @return string
     */
    public function getEngineType()
    {
        return $this->engineType;
    }

    /**
     * @param string $engineType
     */
    public function setEngineType($engineType)
    {
        $this->engineType = $engineType;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMake()
    {
        return $this->make;
    }

    /**
     * @param string $make
     */
    public function setMake($make)
    {
        $this->make = $make;
    }

    /**
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return DateTime
     */
    public function getManufactureDate()
    {
        return $this->manufactureDate;
    }

    /**
     * @param DateTime $manufactureDate
     */
    public function setManufactureDate(DateTime $manufactureDate = null)
    {
        $this->manufactureDate = $manufactureDate;
    }
}
