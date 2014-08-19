<?php

namespace Tystr\RedisOrm\Hydrator;

use Doctrine\Common\Annotations\Annotation;
use Tystr\RedisOrm\DataTransformer\TimestampToDatetimeTransformer;
use Tystr\RedisOrm\DataTransformer\DataTypes;
use Tystr\RedisOrm\Metadata\Metadata;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class ObjectHydrator implements ObjectHydratorInterface
{
    /**
     * @param object   $object
     * @param array    $data
     * @param Metadata $metadata
     * @return object
     */
    public function hydrate($object, array $data, Metadata $metadata)
    {
        $reflClass = new \ReflectionClass(get_class($object));
        foreach ($reflClass->getProperties() as $property) {
            $mapping = $metadata->getPropertyMapping($property->getName());
            $property->setAccessible(true);
            $property->setValue($object, $this->transformValue($mapping['type'], $data[$mapping['name']]));
        }

        return $object;
    }

    /**
     * @param object   $object
     * @param Metadata $metadata
     * @return array
     */
    public function toArray($object, Metadata $metadata)
    {
        $reflClass = new \ReflectionClass(get_class($object));
        $data = array();
        foreach ($reflClass->getProperties() as $property) {
            $mapping = $metadata->getPropertyMapping($property->getName());
            if (null == $mapping) {
                continue;
            }
            $property->setAccessible(true);
            $data[$mapping['name']] = $this->reverseTransformValue($mapping['type'], $property->getValue($object));
        }

        return $data;
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed
     */
    protected function transformValue($type, $value)
    {
        switch ($type) {
            case DataTypes::STRING:
                return strval($value);
            case DataTYpes::INTEGER:
                return intval($value);
            case DataTypes::DOUBLE:
                return doubleval($value);
            case DataTYpes::BOOLEAN:
                return boolval($value);
            default:
                // @todo Lookup custom data transformer for custom configured types?
                return null;
        }
    }

    /**
     * @param string $type
     * @param mixed $value
     * @return mixed|string
     */
    protected function reverseTransformValue($type, $value)
    {
        if ($type == DataTypes::DATE && $value instanceof \DateTime) {
            $transformer = new TimestampToDatetimeTransformer();

            return $transformer->reverseTransform($value);
        }

        return $value;
    }
}
