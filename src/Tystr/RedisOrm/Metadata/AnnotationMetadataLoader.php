<?php

namespace Tystr\RedisOrm\Metadata;

use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Config\ConfigCache;
use ReflectionClass;
use ReflectionProperty;
use Tystr\RedisOrm\Annotations\Id;
use Tystr\RedisOrm\Annotations\Index;
use Tystr\RedisOrm\Annotations\Prefix;
use Tystr\RedisOrm\Annotations\SortedIndex;
use Tystr\RedisOrm\Metadata\Metadata;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class AnnotationMetadataLoader extends Loader
{
    /**
     * @var Metadata
     */
    protected $metadata;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @param string $cacheDir
     */
    public function __construct($cacheDir)
    {
        $this->cacheDir = $cacheDir;
    }

    /**
     * @param object $resource
     * @param null $type
     * @return Metadata
     */
    public function load($resource, $type = null)
    {
        $cachePath = sprintf(
            '/%s/__CG__%s_Metadata.php',
            $this->cacheDir,
            str_replace('\\', '_', $resource)
        );
        $cache = new ConfigCache($cachePath, false);

        if ($cache->isFresh()) {
            return require $cachePath;
        }
        $this->metadata = new Metadata();

        $reader = new AnnotationReader();
        $reflClass = new ReflectionClass($resource);

        $this->loadClassAnnotations($reader, $reflClass);
        $this->loadPropertyAnnotations($reader, $reflClass);

        $code = "<?php\n\nreturn ".var_export($this->metadata, true).';';
        $cache->write($code);

        return $this->metadata;
    }

    /**
     * @param mixed $resource
     * @param null $type
     * @return bool
     */
    public function supports($resource, $type = null)
    {
        return is_string($resource) && class_exists($resource);
    }

    /**
     * @param AnnotationReader $reader
     * @param ReflectionClass $reflClass
     */
    protected function loadClassAnnotations(AnnotationReader $reader, ReflectionClass $reflClass)
    {
        foreach ($reader->getClassAnnotations($reflClass) as $annotation) {
            if ($annotation instanceof Prefix) {
                $this->metadata->setPrefix($annotation->value);
            }
        }
    }

    /**
     * @param AnnotationReader $reader
     * @param \ReflectionClass $reflClass
     */
    protected function loadPropertyAnnotations(AnnotationReader $reader, ReflectionClass $reflClass)
    {
        foreach ($reflClass->getProperties() as $property) {
            foreach ($reader->getPropertyAnnotations($property) as $annotation) {
                if ($annotation instanceof SortedIndex) {
                    $this->metadata->addSortedIndex(
                        $property->getName(), $this->getKeyNameFromAnnotation($property, $annotation)
                    );
                } elseif ($annotation instanceof Index) {
                    $this->metadata->addIndex(
                        $property->getName(), $this->getKeyNameFromAnnotation($property, $annotation)
                    );
                } elseif ($annotation instanceof Id) {
                    $this->metadata->setId($property->getName());
                }
            }
        }
    }

    /**
     * @param ReflectionProperty $property
     * @param object              $annotation
     * @return string
     */
    protected function getKeyNameFromAnnotation(ReflectionProperty $property, $annotation)
    {
        if (!is_object($annotation) || !property_exists($annotation, 'name')) {
            return $property->getName();
        }

        return null === $annotation->name ? $property->getName() : $annotation->name;
    }
}