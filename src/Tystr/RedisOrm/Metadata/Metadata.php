<?php

namespace Tystr\RedisOrm\Metadata;

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

        return $metadata;
    }
}