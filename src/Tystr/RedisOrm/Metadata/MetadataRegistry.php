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
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir = '/tmp')
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param string $class
     * @return Metadata
     */
    public function getMetadataFor($class)
    {
        if (!isset($this->metadata[$class])) {
            $loader = new AnnotationMetadataLoader($this->cacheDir);
            $this->metadata[$class] = $loader->load($class);
        }

        return $this->metadata[$class];
    }
}