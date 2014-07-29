<?php
namespace Tystr\RedisOrm\Context;

use Behat\Gherkin\Node\TableNode;
use Tystr\RedisOrm\KeyNamingStrategy\ColonDelimitedKeyNamingStrategy;
use Tystr\RedisOrm\Repository\PredisRepository;
use Tystr\RedisOrm\Test\Model\Car;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MainContext extends BaseContext
{
    /**
     * @var PredisRepository
     */
    protected $repository;

    public function __construct()
    {
        parent::__construct();

        $keyNamingStrategy = new ColonDelimitedKeyNamingStrategy();
        $this->repository = new PredisRepository($this->redis, $keyNamingStrategy);
    }

    /**
     * @Given /the following Cars?:/
     */
    public function theFollowingCars(TableNode $table)
    {
        foreach ($table->getHash() as $data) {
            $car = new Car();
            $car->setId(1);
            $car->setColor($data['color']);
            $car->setEngineType($data['engine_type']);
            $car->setMake($data['make']);
            $car->setModel($data['model']);
            $car->setManufactureDate(new \DateTime('2013-01-01'));
            $this->repository->save($car);
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
}
