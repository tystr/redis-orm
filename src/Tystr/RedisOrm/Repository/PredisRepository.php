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
use Tystr\RedisOrm\Annotations\Sortable;
use Tystr\RedisOrm\Exception\InvalidArgumentException;
use Tystr\RedisOrm\KeyNamingStrategy\KeyNamingStrategyInterface;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class PredisRepository
{
    /**
     * @var Client
     */
    protected $redis;

    /**
     * @var KeyNamingStrategyInterface
     */
    protected $keyNamingStrategy;

    /**
     * @param Client $redis
     */
    public function __construct(Client $redis, KeyNamingStrategyInterface $keyNamingStrategy)
    {
        $this->redis = $redis;
        $this->keyNamingStrategy = $keyNamingStrategy;
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

        $reflClass = new ReflectionClass(get_class($object));
        foreach ($reflClass->getProperties() as $property) {
            $this->parseAnnotationsForProperty($object, $property);
        }
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
            if ($annotation instanceof Sortable) {
                $this->handleSortableProperty($object, $property, $annotation);
            } elseif ($annotation instanceof Index) {
                $property->setAccessible(true);
                $key = $this->keyNamingStrategy->getKeyName(
                    [$this->getKeyNameFromAnnotation($annotation, $property), $property->getValue($object)]
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
    public function transformDateValue($value)
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
