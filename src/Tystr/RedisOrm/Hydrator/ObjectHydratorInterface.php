<?php

namespace Tystr\RedisOrm\Hydrator;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
interface ObjectHydratorInterface
{
    /**
     * @param object $object
     * @param array $data
     * @return object
     */
    public function hydrate($object, array $data);

    /**
     * @param object $object
     * @return array
     */
    public function toArray($object);
}
