<?php

namespace Tystr\RedisOrm\Hydrator;

use Doctrine\Common\Annotations\Annotation;
use Doctrine\Common\Annotations\AnnotationReader;
use Tystr\RedisOrm\Annotations\Date;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ObjectHydrator implements ObjectHydratorInterface
{
    /**
     * @param object $user
     * @param array  $data
     * @return object
     */
    public function hydrate($object, array $data)
    {
        $reflClass = new \ReflectionClass(get_class($object));
        $reader = new AnnotationReader();
        foreach ($reflClass->getProperties() as $property) {
            $annotations = $reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                $key = $this->getKeyNameFromAnnotation($property, $annotation);
                if (isset($data[$key])) {
                    if ($annotation instanceof Date) {
                        $property->setAccessible(true);
                        $property->setValue($object, new \DateTime('@'.(int) $data[$key]));
                        unset($data[$key]);
                    } else {
                        $property->setAccessible(true);
                        $property->setValue($object, $data[$key]);
                        unset($data[$key]);
                    }
                }
            }
        }


        return $object;
    }

    /**
     * @param object $object
     */
    public function toArray($object)
    {
        $reflClass = new \ReflectionClass(get_class($object));
        $reader = new AnnotationReader();
        $data = array();
        foreach ($reflClass->getProperties() as $property) {
            $annotations = $reader->getPropertyAnnotations($property);
            foreach ($annotations as $annotation) {
                $key = $this->getKeyNameFromAnnotation($property, $annotation);
                $property->setAccessible(true);
                $value = $property->getValue($object);
                if ($annotation instanceof Date) {
                    // @todo type check for datetiem
                    $data[$key] = $value instanceof \DateTime ? $value->format('U') : $value;
                } else {
                    $data[$key] = $property->getValue($object);
                }
            }
        }

        return $data;
    }

    /**
     * @param \ReflectionProperty $property
     * @param Annotation $annotation
     * @return string
     */
    protected function getKeyNameFromAnnotation(\ReflectionProperty $property, Annotation $annotation)
    {
        return null === $annotation->name ? $property->getName() : $annotation->name;
    }
}
