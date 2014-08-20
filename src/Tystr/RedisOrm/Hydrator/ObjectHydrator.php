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
            if (null == $mapping) {
                continue;
            }
            $property->setAccessible(true);
            if (DataTypes::COLLECTION == $mapping['type']) {
                $value = array();
                foreach (array_keys($data) as $key) {
                    if (0 === stripos($key, $mapping['name'].':')) {
                        $value[] = $data[$key];
                    }
                }
                $property->setValue($object, $value);

                continue;
            } elseif (DataTypes::HASH == $mapping['type']) {
                $value = array();
                foreach (array_keys($data) as $key) {
                    if (0 === stripos($key, $mapping['name'].':')) {
                        $newKey = substr($key, strrpos($key, ':')+1);
                        $value[$newKey] = $data[$key];
                    }
                }
                $property->setValue($object, $value);

                continue;
            }

            if (!isset($data[$mapping['name']])) {
                $data[$mapping['name']] = null;
            }
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
            if ($mapping['type'] == DataTypes::COLLECTION || DataTypes::HASH == $mapping['type']) {
                foreach ((array)$property->getValue($object) as $key => $value) {
                    $data[$mapping['name'].':'.$key] = $value;
                }
            } else {
                $data[$mapping['name']] = $this->reverseTransformValue($mapping['type'], $property->getValue($object));
            }
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
            case DataTypes::COLLECTION:
                return (array) $value;
            case DataTypes::DATE:
                if (null === $value || '' === $value) {
                    return null;
                }
                $transformer = new TimestampToDatetimeTransformer();

                return $transformer->transform($value);
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
