<?php
namespace Tystr\RedisOrm\Context;

use Behat\Gherkin\Node\TableNode;
use Doctrine\Common\Collections\ArrayCollection;
use Tystr\RedisOrm\Criteria\Criteria;
use Tystr\RedisOrm\KeyNamingStrategy\ColonDelimitedKeyNamingStrategy;
use Tystr\RedisOrm\Repository\ObjectRepository;
use Tystr\RedisOrm\Test\Model\Car;
use Tystr\RedisOrm\Test\Model\User;
use Tystr\RedisOrm\Test\Model\UserList;
use Tystr\RedisOrm\Criteria\Restrictions;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MainContext extends BaseContext
{
    /**
     * @var ObjectRepository
     */
    protected $repository;

    /**
     * @var ObjectRepository
     */
    protected $userRepository;

    /**
     * @var array
     */
    protected $lists = array();

    /**
     * @var array|Car[]
     */
    protected $cars = array();

    public function __construct()
    {
        parent::__construct();

        $keyNamingStrategy = new ColonDelimitedKeyNamingStrategy();
        $this->repository = new ObjectRepository(
            $this->redis,
            $keyNamingStrategy,
            'Tystr\RedisOrm\Test\Model\Car'
        );
        $this->userRepository = new ObjectRepository(
            $this->redis,
            $keyNamingStrategy,
            'Tystr\RedisOrm\Test\Model\User'
        );
    }

    /**
     * @Given /the following Cars?:/
     */
    public function theFollowingCars(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $car = new Car();
            $car->setId($data['id']);
            $car->setColor($data['color']);
            $car->setEngineType($data['engine_type']);
            $car->setMake($data['make']);
            $car->setModel($data['model']);
            $car->setManufactureDate(new \DateTime('2013-01-01'));
            $this->repository->save($car);
        }
    }
    /**
     * @Given the car with id :id has the property :propertyName with the following values:
     */
    public function theCarWithIdHasThePropertyWithTheFollowingValues($id, $propertyName, TableNode $values)
    {
        $car = $this->repository->find($id);
        $data = array();
        foreach ($values->getRowsHash() as $key => $value) {
            $data[$key] = $value;
        }
        $setter = 'set'.ucfirst(strtolower($propertyName));
        $car->$setter($data);
        $this->repository->save($car);
    }


    /**
     * @Then the car with the id :id should have property :propertyName with the following values:
     */
    public function theCarWithTheIdShouldHavePropertyWithTheFollowingValues($id, $propertyName, TableNode $values)
    {
        $car = $this->repository->find($id);
        $getter = 'get'.ucfirst(strtolower($propertyName));
        $data = $car->$getter();
        foreach ($values->getRowsHash() as $key => $value) {
            assertTrue(isset($data[$key]));
            assertEquals($value, $data[$key]);
        }
    }

    /**
     * @Then there should be :count keys in the database
     */
    public function thereShouldBeKeysInTheDatabase($count)
    {
        assertCount($count, $this->redis->keys('*'));
    }

    /**
     * @Then the following keys should exist:
     */
    public function theFollowingKeysShouldExist(TableNode $table)
    {
        foreach ($table->getHash() as $key) {
            assertTrue($this->redis->sismember($key['name'], $key['value']));
        }
    }

    /**
     * @When I find a Car by id :id
     */
    public function iFindACarById($id)
    {
        $this->cars[] = $this->repository->find($id);
    }

    /**
     * @Then there should be :count car
     */
    public function iThereShouldBeCarReturned($count)
    {
        assertCount($count, $this->cars);
        assertInstanceOf('Tystr\RedisOrm\Test\Model\Car', $this->cars[0]);
    }

    /**
     * @Then the car with the id :arg1 should have the following properties:
     */
    public function theCarWithTheIdShouldHaveTheFollowingProperties($id, TableNode $table)
    {
        $car = $this->getObjectById($id);

        $expected = $table->getHash();
        assertEquals($expected[0]['make'], $car->getMake());
        assertEquals($expected[0]['model'], $car->getModel());
        assertEquals($expected[0]['engine_type'], $car->getEngineType());
        assertEquals($expected[0]['color'], $car->getColor());
    }

    /**
     * @When I set the manufacture date to null
     */
    public function iSetTheManufactureDateToNull()
    {
        $car = $this->getObjectById(1);
        $car->setManufactureDate(null);
        $this->repository->save($car);
    }

    /**
     * @Then When I set the color for the car :id to :color
     */
    public function whenISetTheColorForTheCarTo($id, $color)
    {
        $car = $this->getObjectById($id);
        $color = $color == 'null' ? null : $color;
        $car->setColor($color);
        $this->repository->save($car);
    }

    /**
     * @Then there should be :count items in the :key key
     */
    public function thereShouldBeItemsInTheKey($count, $key)
    {
        $type = $this->redis->type($key);
        if ('set' == $type) {
            assertEquals($count, $this->redis->scard($key));
        } else {
            assertEquals($count, $this->redis->zcard($key));
        }
    }

    /**
     * @Given the following users:
     */
    public function theFollowingUsers(TableNode $table)
    {
        foreach ($table->getHash() as $row) {
            $email = $row['email'];
            unset($row['email']);
            $dob = $row['dob'];
            unset($row['dob']);
            $signup = $row['signup'];
            unset($row['signup']);
            $lastOpen = $row['last_open'];
            unset($row['last_open']);
            $lastClick = $row['last_click'];
            unset($row['last_click']);

            $user = new User($email, $row);
            $user->setDateOfBirth(new \DateTime($dob));
            $user->setSignupDate(new \DateTime($signup));
            $user->setLastOpen(new \DateTime($lastOpen));
            $user->setLastClick(new \DateTime($lastClick));
            $this->userRepository->save($user);
        }
    }

    /**
     * @Given the list :listName has the following criteria:
     */
    public function theListHasTheFollowingCriteria($listName, TableNode $table)
    {
        $criteria = new Criteria(new ArrayCollection());
        foreach ($table->getHash() as $restriction) {
            if (in_array($restriction['key'], array('signup', 'last_open', 'last_click', 'dob'))) {
                $value = new \DateTime($restriction['value']);
                $value = $value->format('U');
            } else {
                $value = $restriction['value'];
            }
            $method = $restriction['name'];
            $criteria->addRestriction(
                Restrictions::$method($restriction['key'], $value)
            );
        }

        $this->lists[$listName] = $criteria;
    }

    /**
     * @Then the list :listName should have :count users
     */
    public function theListShouldHaveRecipients($listName, $count)
    {
        assertCount($count, $this->userRepository->findBy($this->lists[$listName]));
    }

    /**
     * @param int$id
     * @return object
     */
    public function getObjectById($id)
    {
        $object = $this->repository->find($id);
        assertNotNull($object);

        return $object;
    }
}
