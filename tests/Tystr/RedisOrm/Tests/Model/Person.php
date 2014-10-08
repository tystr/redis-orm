<?php

namespace Tystr\RedisOrm\Tests\Model;

use Tystr\RedisOrm\Annotations\Field;
use Tystr\RedisOrm\Annotations\Id;
use Tystr\RedisOrm\Annotations\Index;
use Tystr\RedisOrm\Annotations\Prefix;
use Tystr\RedisOrm\Annotations\SortedIndex;

/**
 * @Prefix("person")
 */
class Person
{
    /**
     * @Id
     * @Field(type="integer")
     * @var int
     */
    public $id;

    /**
     * @Field(type="string", name="first_name")
     * @Index
     * @var string
     */
    public $firstName;

    /**
     * @Field(type="string", name="last_name")
     * @Index
     * @var string
     */
    public $lastName;

    /**
     * @Field(type="date")
     * @SortedIndex
     * @var \DateTime
     */
    public $dob;

    /**
     * @Field(type="string")
     * @Index
     * @var string
     */
    public $address;

    /**
     * @Field(type="string")
     * @Index
     * @var string
     */
    public $city;

    /**
     * @Field(type="string")
     * @Index
     * @var string
     */
    public $state;

    /**
     * @Field(type="string")
     * @Index
     * @var string
     */
    public $zip;

    /**
     * @Field(type="integer")
     * @Index
     * @var string
     */
    public $impressions;

    /**
     * @Field(type="integer")
     * @Index
     * @var string
     */
    public $clicks;

    /**
     * @Field(type="double")
     * @Index
     * @var float
     */
    public $money;
    /**
     * @param string    $name
     * @param \DateTime $dob
     */
    public function __construct($name, \DateTime $dob = null)
    {
        $this->name = $name;
        $this->dob = $dob;
    }
}