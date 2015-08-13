<?php

namespace Tystr\RedisOrm\Criteria;

use PHPUnit_Framework_TestCase;

/**
 * @author Justin Taft <justin.t@zeetogroup.com>
 */
class RestrictionsKeyGeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @param $restrictions
     * @param $expectedKey
     * @dataProvider dataProvider
     */
    public function testKeyGenerator($restrictions, $expectedKey)
    {
        $keyGenerator = new RestrictionsKeyGenerator();
        assertEquals($expectedKey, $keyGenerator->getKeyName($restrictions));
    }

    public function dataProvider()
    {
        return array(
           array(array(new EqualTo('aKey', 'aValue')), 'aKey Tystr\RedisOrm\Criteria\EqualTo aValue'),
           array(array(new EqualTo('aKey', 'aValue'), new EqualTo('aKey2', 'aValue2')), 'aKey Tystr\RedisOrm\Criteria\EqualTo aValue, aKey2 Tystr\RedisOrm\Criteria\EqualTo aValue2'),
           array(array(new AndGroup('aKey', array())), 'aKey Tystr\RedisOrm\Criteria\AndGroup ()'),
           array(array(new AndGroup('aKey', array(new EqualTo('equalKey', 'equalValue')))), 'aKey Tystr\RedisOrm\Criteria\AndGroup (equalKey Tystr\RedisOrm\Criteria\EqualTo equalValue)'),
           array(array(new OrGroup('aKey', array(new EqualTo('equalKey', 'equalValue')))), 'aKey Tystr\RedisOrm\Criteria\OrGroup (equalKey Tystr\RedisOrm\Criteria\EqualTo equalValue)'),
           array(array(new AndGroup('aKey', array(new EqualTo('equal1', 'value1'), new EqualTo('equal2', 'value2')))), 'aKey Tystr\RedisOrm\Criteria\AndGroup (equal1 Tystr\RedisOrm\Criteria\EqualTo value1, equal2 Tystr\RedisOrm\Criteria\EqualTo value2)'),
           array(array(new AndGroup('andKey1', array()),new AndGroup('andKey2', array())), 'andKey1 Tystr\RedisOrm\Criteria\AndGroup (), andKey2 Tystr\RedisOrm\Criteria\AndGroup ()'),
        );
    }
}
