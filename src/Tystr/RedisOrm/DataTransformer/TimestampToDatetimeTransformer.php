<?php

namespace Tystr\RedisOrm\DataTransformer;

use DateTime;
use Tystr\RedisOrm\Exception\InvalidArgumentException;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class TimestampToDatetimeTransformer
{
    /**
     * @param mixed $value
     * @return string
     */
    public function transform($value)
    {
        return DateTime::createFromFormat('U', $value);
    }

    /**
     * @param mixed $value
     * @return mixed|string
     */
    public function reverseTransform($value)
    {
        if (!$value instanceof DateTime) {
            throw new InvalidArgumentException(
                sprintf(
                    'The value must be an instance of \DateTime, "%s given.',
                    gettype($value)
                )
            );
        }

        return $value->format('U');
    }
}