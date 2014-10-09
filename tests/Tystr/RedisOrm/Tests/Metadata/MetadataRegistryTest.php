<?php

namespace Tystr\RedisOrm\Metadata;

use Tystr\RedisOrm\Hydrator\ObjectHydrator;
use Tystr\RedisOrm\Metadata\MetadataRegistry;
use Tystr\RedisOrm\Tests\Model\Person;
use Tystr\RedisOrm\Metadata\Metadata;
use DateTime;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class MetadataRegistryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMetadataForWith()
    {
        $loader = $this->getMockBuilder('Tystr\RedisOrm\Metadata\AnnotationMetadataLoader')->disableOriginalConstructor()->getMock();
        $registry = new MetadataRegistry($loader);
        $class = 'Tystr\RedisOrm\Tests\Model\Person';

        $expectedMetadata = new Metadata();

        $loader->expects($this->once())
            ->method('load')
            ->with($class)
            ->will($this->returnValue($expectedMetadata));

        $metadata = $registry->getMetadataFor($class);
        $this->assertSame($expectedMetadata, $metadata);

        // The following call should not trigger a call to LoaderInterface::load()
        $metadata  = $registry->getMetadataFor($class);
        $this->assertSame($expectedMetadata, $metadata);
    }
}