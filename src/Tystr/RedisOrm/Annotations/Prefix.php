<?php

namespace Tystr\RedisOrm\Annotations;

use Doctrine\Common\Annotations\Annotation;

/**
 * @Annotation
 * @Target({"CLASS"})
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
final class Prefix extends Annotation
{
    /**
     * @var string
     */
    public $name;
}
