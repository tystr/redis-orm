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
        $dateTime = DateTime::createFromFormat('U', $value);
        if (false === $dateTime) {
            throw new InvalidArgumentException(
                sprintf(
                    'Cannot transform "%s" into a DateTime object. The value must be a valid unix timestamp',
                    $value
                )
            );
        }

        return $dateTime;
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