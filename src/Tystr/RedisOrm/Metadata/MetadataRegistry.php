<?php

namespace Tystr\RedisOrm\Metadata;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MetadataRegistry
{
    /**
     * @var array
     */
    protected $metadata;

    /**
     * @param string $class
     * @return Metadata
     */
    public function getMetadataFor($class)
    {
        if (!isset($this->metadata[$class])) {
            $loader = new AnnotationMetadataLoader('/tmp');
            $this->metadata[$class] = $loader->load($class);
        }

        return $this->metadata[$class];
    }
}