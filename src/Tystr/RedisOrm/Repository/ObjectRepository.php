<?php

namespace Tystr\RedisOrm\Repository;

use Doctrine\Common\Annotations\Annotation;
use Predis\Client;
use Doctrine\Common\Annotations\AnnotationReader;
use ReflectionClass;
use ReflectionProperty;
use DateTime;
use Tystr\RedisOrm\Annotations\Date;
use Tystr\RedisOrm\Annotations\Index;
use Tystr\RedisOrm\Annotations\SortedIndex;
use Tystr\RedisOrm\DataTransformer\DataTypes;
use Tystr\RedisOrm\DataTransformer\TimestampToDatetimeTransformer;
use Tystr\RedisOrm\Exception\InvalidArgumentException;
use Tystr\RedisOrm\Hydrator\ObjectHydrator;
use Tystr\RedisOrm\Hydrator\ObjectHydratorInterface;
use Tystr\RedisOrm\KeyNamingStrategy\KeyNamingStrategyInterface;
use Tystr\RedisOrm\Metadata\AnnotationMetadataLoader;
use Tystr\RedisOrm\Metadata\Metadata;
use Tystr\RedisOrm\Metadata\MetadataRegistry;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ObjectRepository
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var KeyNamingStrategyInterface
     */
    protected $keyNamingStrategy;

    /**
     * @var string
     */
    protected $prefix;

    /**
     * @var ObjectHydratorInterface
     */
    protected $hydrator;

    /**
     * @param Client                     $redis
     * @param KeyNamingStrategyInterface $keyNamingStrategy
     * @param string                     $className
     * @param ObjectHydratorInterface    $objectHydrator
     */
    public function __construct(
        Client $redis,
        KeyNamingStrategyInterface $keyNamingStrategy,
        $className,
        ObjectHydratorInterface $objectHydrator = null
    ) {
        $this->redis = $redis;
        $this->keyNamingStrategy = $keyNamingStrategy;
        $this->className = $className;
        $this->hydrator = $objectHydrator ?: new ObjectHydrator();
    }

    /**
     * @param object $object
     */
    public function save($object)
    {
        if (!is_object($object)) {
            throw new InvalidArgumentException(
                sprintf(
                    'You must pass an object to Tystr\RedisOrm\Repository\PredisRepository::save(), %s given.',
                    gettype($object)
                )
            );
        }

        $metadata = $this->getMetadataFor($this->className);
        $key = $this->keyNamingStrategy->getKeyName(array($metadata->getPrefix(), $this->getIdForClass($object, $metadata)));
        $originalData = $this->redis->hgetall($key);
        $this->redis->hmset(
            $key,
            $newData = $this->hydrator->toArray($object, $metadata)
        );
        $this->handleProperties($object, $metadata, $originalData, $newData);
    }

    /**
     * @param mixed $id
     * @return object
     */
    public function find($id)
    {
        $metadata = $this->getMetadataFor($this->className);
        $key = $this->keyNamingStrategy->getKeyName(array($metadata->getPrefix(), $id));
        $data = $this->redis->hgetall($key);

        return $this->hydrator->hydrate($this->newObject(), $data, $metadata);
    }

    /**
     * @param string $className
     * @return Metadata
     */
    protected function getMetadataFor($className)
    {
        $metadataRegistry = new MetadataRegistry();

        return $metadataRegistry->getMetadataFor($className);
    }

    /**
     * @param object   $object
     * @param Metadata $metadata
     * @param array    $originalData
     * @param array    $newData
     */
    protected function handleProperties($object, Metadata $metadata, array $originalData, array $newData)
    {
        $reflClass = new ReflectionClass($object);
        foreach ($metadata->getIndexes() as $propertyName => $keyName) {
            $this->handleIndex($reflClass, $object, $propertyName, $keyName, $metadata, $originalData);
        }

        foreach ($metadata->getSortedIndexes() as $propertyName => $keyName) {
            $this->handleSortedIndex($reflClass, $object, $propertyName, $keyName, $metadata, $newData);
        }
    }

    /**
     * @param ReflectionClass $reflClass
     * @param object          $object
     * @param string          $propertyName
     * @param Metadata        $metadata
     * @param array           $originalData
     */
    protected function handleIndex(ReflectionClass $reflClass, $object, $propertyName, $keyName, Metadata $metadata, array $originalData)
    {
        $property = $reflClass->getProperty($propertyName);
        $property->setAccessible(true);
        $value = $property->getValue($object);
        if (null === $value && isset($originalData[$keyName])) {
            $key = $this->keyNamingStrategy->getKeyName(array($keyName, $originalData[$keyName]));
            $this->redis->srem(
                $key,
                $this->getIdForClass($object, $metadata)
            );
        } else {
            $key = $this->keyNamingStrategy->getKeyName(array($keyName, $value));
            $this->redis->sadd($key, $this->getIdForClass($object, $metadata));
        }
    }

    /**
     * @param ReflectionClass $reflClass
     * @param object          $object
     * @param string          $propertyName
     * @param Metadata        $metadata
     * @param array           $newData
     */
    protected function handleSortedIndex(ReflectionClass $reflClass, $object, $propertyName, $keyName, Metadata $metadata, array $newData)
    {
        $property = $reflClass->getProperty($propertyName);
        $property->setAccessible(true);
        $mapping = $metadata->getPropertyMapping($propertyName);

        if (!isset($newData[$mapping['name']]) || null === $newData[$mapping['name']]) {
            $this->redis->zrem($this->keyNamingStrategy->getKeyName(array($keyName, $newData[$mapping['name']])), $this->getIdForClass($object, $metadata));

            return;
        }

        $this->redis->zadd(
            $this->keyNamingStrategy->getKeyName(array($keyName)),
            $newData[$mapping['name']],
            $this->getIdForClass($object, $metadata)
        );
    }

    /**
     * @param ReflectionClass $reflClass
     * @param Metadata        $metadata
     * @return string|int
     */
    protected function getIdForClass($object, Metadata $metadata)
    {
        $getter = 'get'.ucfirst(strtolower($metadata->getId()));
        if (!method_exists($object, $getter)) {
            throw new \RuntimeException(
                sprintf(
                    'The class "%s" must have a "%s" method for accessing the property mapped as the id field (%s)',
                    get_class($object),
                    $getter,
                    $metadata->getId()
                )
            );
        }

        return $object->$getter();
    }

    /**
     * @return object
     */
    protected function newObject()
    {
        if (version_compare(PHP_VERSION, '5.4') >= 0) {
            $reflClass = new ReflectionClass($this->className);

            return $reflClass->newInstanceWithoutConstructor();
        }

        return unserialize(sprintf('O:%d:"%s":0:{}', strlen($this->className), $this->className));
    }
}
