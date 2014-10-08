<?php

namespace Tystr\RedisOrm\Metadata;

use Tystr\RedisOrm\DataTransformer\DataTypes;
use Tystr\RedisOrm\Exception\InvalidArgumentException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MetadataTest extends \PHPUnit_Framework_TestCase
{
    public function testAddPropertyMappingWithInvalidMappingThrowsException()
    {
        $metadata = new Metadata();
        $this->setExpectedException('InvalidArgumentException');
        $metadata->addPropertyMapping('someProperty', null);
    }

    public function testAddPropertyMappingWithInvalidDataTypeThrowsException()
    {
        $metadata = new Metadata();
        $this->setExpectedException('InvalidArgumentException');
        $metadata->addPropertyMapping('someProperty', array('name' => 'some_property', 'type' => 'INVALID TYPE'));
    }

    public function testAddPropertyMapping()
    {
        $metadata = new Metadata();
        $mapping = array('type' => 'string');
        $metadata->addPropertyMapping('property', $mapping);

        $mapping['name'] = 'property';
        assertEquals($metadata->getPropertyMapping('property'), $mapping);
    }

    public function testAddPropertyMappingWithName()
    {
        $metadata = new Metadata();
        $mapping = array('name' => 'some_property', 'type' => 'string');
        $metadata->addPropertyMapping('someProperty', $mapping);

        assertEquals($metadata->getPropertyMapping('someProperty'), $mapping);
    }

    public function testGetMappingForMappedName()
    {
        $metadata = new Metadata();
        $mapping = array('name' => 'some_property', 'type' => 'string');
        $metadata->addPropertyMapping('someProperty', $mapping);

        $mapping['propertyName'] = 'someProperty';
        assertEquals($mapping, $metadata->getMappingForMappedName('some_property'));
    }

    public function testSetState()
    {
        $data = array(
            'id' => 1,
            'prefix' => 'some_',
            'indexes' => array('index1', 'index2'),
            'sortedIndexes' => array('sortedIndex1', 'sortedIndex2'),
            'propertyMappings' => array(
                array('name' => 'property', 'type' => 'string')
            )
        );

        $metadata = Metadata::__set_state($data);
        assertInstanceOf('Tystr\RedisOrm\Metadata\Metadata', $metadata);
        assertEquals($data['id'], $metadata->getId());
        assertEquals($data['prefix'], $metadata->getPrefix());
        assertEquals($data['indexes'], $metadata->getIndexes());
        assertEquals($data['sortedIndexes'], $metadata->getSortedIndexes());
        assertEquals($data['propertyMappings'], $metadata->getPropertyMappings());
    }
}