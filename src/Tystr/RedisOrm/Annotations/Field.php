<?php

namespace Tystr\RedisOrm\Annotations;

use Doctrine\Common\Annotations\Annotation;
use Tystr\RedisOrm\Metadata\DataTypes;

/**
 * @Annotation
 * @Target({"PROPERTY"})
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
final class Field extends Annotation
{
    public $name;

    public $type;
}