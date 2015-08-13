<?php

namespace Tystr\RedisOrm\Criteria;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
class RestrictionsKeyGenerator
{
    /**
     * @param array $parts
     *
     * @return string
     */
    public function getKeyName(array $parts)
    {
        return $this->_createKeyStringForRestriction($parts);
    }

    private function _createKeyStringForRestriction($restrictions)
    {
        $string = '';
        foreach ($restrictions as $restriction) {
            $value = $restriction->getValue();
            $finalValue = '';

            if (is_array($value) || $value instanceof \Traversable) {
                $finalValue .= '('.$this->_createKeyStringForRestriction($value).')';
            } else {
                $finalValue = $restriction->getValue();
            }

            $string .= sprintf(
                '%s %s %s, ',
                $restriction->getKey(),
                get_class($restriction),
                $finalValue
            );
        }
        return rtrim($string,', ');
    }
}
