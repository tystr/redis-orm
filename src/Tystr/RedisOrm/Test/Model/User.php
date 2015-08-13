<?php

namespace Tystr\RedisOrm\Test\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Tystr\RedisOrm\Annotations\Date;
use Tystr\RedisOrm\Annotations\Field;
use Tystr\RedisOrm\Annotations\Id;
use Tystr\RedisOrm\Annotations\Index;
use Tystr\RedisOrm\Annotations\Prefix;

/**
 * @Prefix("users")
 *
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class User
{
    /**
     * @Id
     *
     * @var string
     */
    protected $email;

    /**
     * @Field(type="date")
     * @Date(name="dob")
     *
     * @var \DateTime
     */
    protected $dateOfBirth;

    /**
     * @Field(type="date")
     * @Date(name="signup")
     *
     * @var \DateTime
     */
    protected $signupDate;

    /**
     * @Field(type="date")
     * @Date(name="last_open")
     *
     * @var \DateTime
     */
    protected $lastOpen;

    /**
     * @Field(type="date")
     * @Date(name="last_click")
     *
     * @var \DateTime
     */
    protected $lastClick;

    /**
     * @Field(type="hash")
     * @Index
     *
     * @var array
     */
    protected $attributes = array();

    /**
     * @param string $email
     * @param array  $attributes
     */
    public function __construct($email, array $attributes = array())
    {
        $this->email = $email;
        $this->attributes = $attributes;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->email;
    }

    /**
     * @return ArrayCollection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = $attributes;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * @param \DateTime $dateOfBirth
     */
    public function setDateOfBirth(\DateTime $dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @return \DateTime
     */
    public function getSignupDate()
    {
        return $this->signupDate;
    }

    /**
     * @param \DateTime $signupDate
     */
    public function setSignupDate(\DateTime $signupDate)
    {
        $this->signupDate = $signupDate;
    }

    /**
     * @return \DateTime
     */
    public function getLastClick()
    {
        return $this->lastClick;
    }

    /**
     * @param \DateTime $lastClick
     */
    public function setLastClick(\DateTime $lastClick)
    {
        $this->lastClick = $lastClick;
    }

    /**
     * @return \DateTime
     */
    public function getLastOpen()
    {
        return $this->lastOpen;
    }

    /**
     * @param \DateTime $lastOpen
     */
    public function setLastOpen(\DateTime $lastOpen)
    {
        $this->lastOpen = $lastOpen;
    }
}
