<?php

namespace Tystr\RedisOrm\Hydrator;
use Tystr\RedisOrm\Metadata\Metadata;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
interface ObjectHydratorInterface
{
    /**
     * @param object   $object
     * @param array    $data
     * @param Metadata $metadata
     * @return object
     */
    public function hydrate($object, array $data, Metadata $metadata);

    /**
     * @param object   $object
     * @param Metadata $metadata
     * @return array
     */
    public function toArray($object, Metadata $metadata);
}
