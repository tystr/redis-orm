<?php

namespace Tystr\RedisOrm\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
final class Date extends SortedIndex
{
    /**
     * @var string
     */
    public $name;
}
