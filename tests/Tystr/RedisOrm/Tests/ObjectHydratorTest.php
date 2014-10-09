<?php

namespace Tystr\RedisOrm\Tests;

use Tystr\RedisOrm\Hydrator\ObjectHydrator;
use Tystr\RedisOrm\Metadata\AnnotationMetadataLoader;
use Tystr\RedisOrm\Metadata\MetadataRegistry;
use Tystr\RedisOrm\Tests\Model\Person;
use Tystr\RedisOrm\Metadata\Metadata;
use DateTime;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ObjectHydratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectHydrator
     */
    protected $hydrator;

    /**
     * @var Person
     */
    protected $person;

    /**
     * @var Metadata
     */
    protected $metadata;

    public function setUp()
    {
        $this->hydrator = new ObjectHydrator();
        $this->person = new Person('Tyler');
        $loader = new AnnotationMetadataLoader('/tmp');
        $registry = new MetadataRegistry($loader);
        $this->metadata = $registry->getMetadataFor(get_class($this->person));
    }

    public function testHydrateInteger()
    {
        $data = array('id' => "1");
        $hydratedPerson = $this->hydrator->hydrate($this->person, $data, $this->metadata);
        assertInternalType('integer', $hydratedPerson->id);
        assertEquals(1, $hydratedPerson->id);
    }

    public function testHydrateString()
    {
        $data = array('address' => "123 Main St.");
        $hydratedPerson = $this->hydrator->hydrate($this->person, $data, $this->metadata);
        assertInternalType('string', $hydratedPerson->address);
        assertEquals('123 Main St.', $hydratedPerson->address);
    }

    public function testHydrateFloat()
    {
        $data = array('money' => "10.95");
        $hydratedPerson = $this->hydrator->hydrate($this->person, $data, $this->metadata);
        assertInternalType('float', $hydratedPerson->money);
        assertEquals(10.95, $hydratedPerson->money);
    }

    public function testHydrateDateTime()
    {
        $dob = new \DateTime('1989-01-01');
        $data = array('dob' => $dob->format('U'));
        $hydratedPerson = $this->hydrator->hydrate($this->person, $data, $this->metadata);
        assertInstanceOf('DateTime', $hydratedPerson->dob);
        assertEquals('1989-01-01', $hydratedPerson->dob->format('Y-m-d'));
    }

    public function testHydrateWithName()
    {
        $data = array('first_name' => 'Tyler');
        $hydratedPerson = $this->hydrator->hydrate($this->person, $data, $this->metadata);

        assertEquals('Tyler', $hydratedPerson->firstName);
    }

    public function testToArrayWithString()
    {
        $this->person->address = '123 Main St.';
        $array = $this->hydrator->toArray($this->person, $this->metadata);

        assertArrayHasKey('address', $array);
        assertInternalType('string', $array['address']);
        assertEquals('123 Main St.', $array['address']);
    }

    public function testToArrayWithInteger()
    {
        $this->person->id = 10;
        $array = $this->hydrator->toArray($this->person, $this->metadata);

        assertArrayHasKey('id', $array);
        assertInternalType('integer', $array['id']);
        assertEquals(10, $array['id']);
    }

    public function testToArrayWithFloat()
    {
        $this->person->money = 9.95;
        $array = $this->hydrator->toArray($this->person, $this->metadata);

        assertArrayHasKey('money', $array);
        assertInternalType('float', $array['money']);
        assertEquals(9.95, $array['money']);
    }

    public function testToArrayWithDateTime()
    {
        $this->person->dob = new DateTime('1984-01-01');
        $array = $this->hydrator->toArray($this->person, $this->metadata);

        assertArrayHasKey('dob', $array);
        assertInternalType('string', $array['dob']);
        assertEquals('441763200', $array['dob']);
    }

    public function testToArrayWithName()
    {
        $this->person->firstName = 'Tyler';
        $array = $this->hydrator->toArray($this->person, $this->metadata);

        assertArrayHasKey('first_name', $array);
        assertInternalType('string', $array['first_name']);
        assertEquals('Tyler', $array['first_name']);
    }
}