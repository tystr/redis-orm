<?php

namespace Tystr\RedisOrm\Metadata;

use Tystr\RedisOrm\DataTransformer\DataTypes;
use Tystr\RedisOrm\Exception\InvalidArgumentException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Metadata
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var array
     */
    protected $indexes = array();

    /**
     * @var array
     */
    protected $sortedIndexes = array();

    /**
     * @var array
     */
    protected $propertyMappings = array();

    /**
     * @return array
     */
    public function getIndexes()
    {
        return $this->indexes;
    }

    /**
     * @param array $indexes
     */
    public function setIndexes($indexes)
    {
        $this->indexes = $indexes;
    }

    /**
     * @param string $property
     * @param string $index
     */
    public function addIndex($property, $index)
    {
        $this->indexes[$property] = $index;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasIndex($name)
    {
        return isset($this->indexes[$name]);
    }

    /**
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * @param string $prefix
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;
    }

    /**
     * @return array
     */
    public function getSortedIndexes()
    {
        return $this->sortedIndexes;
    }

    /**
     * @param array $sortedIndexes
     */
    public function setSortedIndexes($sortedIndexes)
    {
        $this->sortedIndexes = $sortedIndexes;
    }

    /**
     * @param string $propertyName
     * @param string $sortedIndex
     */
    public function addSortedIndex($propertyName, $sortedIndex)
    {
        $this->sortedIndexes[$propertyName] = $sortedIndex;
    }

    /**
     * @param string $propertyName
     * @return string|null
     */
    public function getSortedIndex($propertyName)
    {
        if (isset($this->sortedIndexes[$propertyName])) {
            return $this->sortedIndexes[$propertyName];
        }

        return null;
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
     * @return array
     */
    public function getPropertyMappings()
    {
        return $this->propertyMappings;
    }

    /**
     * @param array $propertyMappings
     */
    public function setPropertyMappings(array $propertyMappings)
    {
        $this->propertyMappings = $propertyMappings;
    }

    /**
     * @param string $propertyName
     * @param string $mapping
     */
    public function addPropertyMapping($propertyName, $mapping)
    {
        if (!isset($mapping['type'])) {
            throw new InvalidArgumentException(sprintf('Invalid @Field mapping for property "%s".', $propertyName));
        }
        if (!DataTypes::isValidDataType($mapping['type'])) {
            throw new InvalidArgumentException(
                sprintf(
                    'Invalid @Field mapping for property "%s": the specified type "%s" is invalid.',
                    $propertyName,
                    $mapping['type']
                )
            );
        }
        $this->propertyMappings[$propertyName]['type'] = $mapping['type'];
        $this->propertyMappings[$propertyName]['name'] = isset($mapping['name']) && null !== $mapping['name'] ?
            $mapping['name'] : $propertyName;
    }

    /**
     * @param string $propertyName
     * @return null
     */
    public function getPropertyMapping($propertyName)
    {
        if (isset($this->propertyMappings[$propertyName])) {
            return $this->propertyMappings[$propertyName];
        }

        return null;
    }


    /**
     * @param array $array
     * @return Metadata
     */
    static public function __set_state(array $array)
    {
        $metadata = new static();
        $metadata->setId($array['id']);
        $metadata->setPrefix($array['prefix']);
        $metadata->setIndexes($array['indexes']);
        $metadata->setSortedIndexes($array['sortedIndexes']);
        $metadata->setPropertyMappings($array['propertyMappings']);

        return $metadata;
    }
}