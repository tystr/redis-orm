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
     */
    public function __construct(Client $redis, KeyNamingStrategyInterface $keyNamingStrategy, $className)
    {
        $this->redis = $redis;
        $this->keyNamingStrategy = $keyNamingStrategy;
        $this->className = $className;
        $this->hydrator = new ObjectHydrator();
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
            $this->hydrator->toArray($object)
        );
        $this->handleProperties($object, $metadata, $originalData);
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

        return $this->hydrator->hydrate($this->newObject(), $data);
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
     */
    protected function handleProperties($object, Metadata $metadata, array $originalData)
    {
        $reflClass = new ReflectionClass($object);
        foreach ($metadata->getIndexes() as $propertyName => $keyName) {
            $this->handleIndex($reflClass, $object, $propertyName, $keyName, $metadata, $originalData);
        }

        foreach ($metadata->getSortedIndexes() as $propertyName => $keyName) {
            $this->handleSortedIndex($reflClass, $object, $propertyName, $keyName, $metadata, $originalData);
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
     * @param array           $originalData
     */
    protected function handleSortedIndex(ReflectionClass $reflClass, $object, $propertyName, $keyName, Metadata $metadata, array $originalData)
    {
        $property = $reflClass->getProperty($propertyName);
        $property->setAccessible(true);
        $value = $property->getValue($object);
        if (null === $value) {
            $this->redis->zrem($this->keyNamingStrategy->getKeyName(array($keyName, $value)), $this->getIdForClass($object, $metadata));

            return;
        }

        // @TODO FIGURE OUT IF DATE OR HOW TO HANDLE THIS
        //if ($annotation instanceof Date) {
            $value = $this->transformDateValue($value);
        //}

        $this->redis->zadd(
            $this->keyNamingStrategy->getKeyName(array($keyName)),
            $value,
            $this->getIdForClass($object, $metadata)
        );
    }

    /**
     * @TODO This doesn't belong in here
     *
     * @param DateTime $value
     * @return int
     */
    protected function transformDateValue($value)
    {
        if (!$value instanceof DateTime) {
            throw new \RuntimeException(
                sprintf(
                    'The value of fields with the "Tystr\RedisOrm\Annotations\Date" annotation must be \DateTime, found "%s" instead.',
                    is_object($value) ? get_class($value): gettype($value)
                )
            );
        }

        return $value->format('U');
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
