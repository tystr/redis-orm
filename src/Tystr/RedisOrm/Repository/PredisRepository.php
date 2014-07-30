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

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class PredisRepository
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
     * @param string                     $prefix
     */
    public function __construct(Client $redis, KeyNamingStrategyInterface $keyNamingStrategy, $className, $prefix)
    {
        $this->redis = $redis;
        $this->keyNamingStrategy = $keyNamingStrategy;
        $this->className = $className;
        $this->prefix = $prefix;
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

        $this->redis->hmset(
            $this->keyNamingStrategy->getKeyName(array($this->prefix, $this->getIdForClass($object))),
            $this->hydrator->toArray($object)
        );
        $reflClass = new ReflectionClass(get_class($object));
        foreach ($reflClass->getProperties() as $property) {
            $this->parseAnnotationsForProperty($object, $property);
        }
    }

    /**
     * @param mixed $id
     * @return object
     */
    public function find($id)
    {
        $key = $this->keyNamingStrategy->getKeyName(array($this->prefix, $id));
        $data = $this->redis->hgetall($key);

        $reflClass = new ReflectionClass($this->className);
        if (version_compare(PHP_VERSION, '5.4') >= 0) {
            $object = $reflClass->newInstanceWithoutConstructor();
        } else {
            $object = unserialize(sprintf('O:%d:"%s":0:{}', strlen($this->className), $this->className));
        }

        return $this->hydrator->hydrate($object, $data);
    }

    /**
     * @param $object
     * @param ReflectionProperty $property
     */
    protected function parseAnnotationsForProperty($object, ReflectionProperty $property)
    {
        $reader = new AnnotationReader();
        $annotations = $reader->getPropertyAnnotations($property);
        foreach ($annotations as $annotation) {
            if ($annotation instanceof SortedIndex) {
                $this->handleSortableProperty($object, $property, $annotation);
            } elseif ($annotation instanceof Index) {
                $property->setAccessible(true);
                $key = $this->keyNamingStrategy->getKeyName(
                    array($this->getKeyNameFromAnnotation($annotation, $property), $property->getValue($object))
                );
                $this->redis->sadd($key, $this->getIdForClass($object));
            }
        }
    }

    /**
     * @param $object
     * @param ReflectionProperty $property
     * @param Annotation         $annotation
     */
    protected function handleSortableProperty($object, ReflectionProperty $property, Annotation $annotation)
    {
        $property->setAccessible(true);
        $value = $property->getValue($object);
        if (null === $value) {
            return;
        }

        if ($annotation instanceof Date) {
            $value = $this->transformDateValue($value);
        }

        $this->redis->zadd(
            $this->getKeyNameFromAnnotation($annotation, $property),
            $value,
            $this->getIdForClass($object)
        );
    }

    /**
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
     * @param Annotation         $annotation
     * @param ReflectionProperty $property
     * @return string
     */
    protected function getKeyNameFromAnnotation(Annotation $annotation, ReflectionProperty $property)
    {
        return null === $annotation->name ? $property->getName() : $annotation->name;
    }

    /**
     * @param ReflectionClass $reflClass
     * @return string|int
     */
    protected function getIdForClass($object)
    {
        $reader = new AnnotationReader();
        $reflClass = new ReflectionClass($object);
        $id = null;
        foreach ($reflClass->getProperties() as $property) {
            if (null !== $reader->getPropertyAnnotation($property, 'Tystr\RedisOrm\Annotations\Id')) {
                if ($id !== null) {
                    throw new \RuntimeException(
                        sprintf('Only 1 class property can have the "Tystr\RedisOrm\Annotations\Id" annotation.')
                    );
                }
                $property->setAccessible(true);
                $id = $property->getValue($object);
            }
        }

        return $id;
    }
}
