<?php

namespace Tystr\RedisOrm\Metadata;

use Symfony\Component\Config\Loader\LoaderInterface;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MetadataRegistry
{
    /**
     * @var array
     */
    protected $metadata = [];

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @param LoaderInterface $loader
     */
    public function __construct(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * @param string $class
     * @return Metadata
     */
    public function getMetadataFor($class)
    {
        if (!array_key_exists($class, $this->metadata)) {
            $this->metadata[$class] = $this->loader->load($class);
        }

        return $this->metadata[$class];
    }
}