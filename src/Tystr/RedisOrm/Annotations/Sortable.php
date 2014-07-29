<?php

namespace Tystr\RedisOrm\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class Sortable extends Annotation
{
    /**
     * @var string
     */
    public $name;
}
