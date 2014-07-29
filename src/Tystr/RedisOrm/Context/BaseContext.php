<?php
namespace Tystr\RedisOrm\Context;

use Behat\Behat\Context\SnippetAcceptingContext;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Predis\Client;

/**
 * @author Tyler Stroud <tyler@tylerstroud.com>
 */
class BaseContext implements SnippetAcceptingContext
{
    /**
     * @var Client
     */
    protected $redis;

    public function __construct()
    {
        AnnotationRegistry::registerAutoloadNamespace('Tystr\RedisOrm\Annotations', __DIR__.'/../../../');
        $this->redis = new Client();
    }

    /**
     * @BeforeScenario
     */
    public function flushRedis()
    {
        $this->redis->flushall();
    }

    /**
     * @Transform /^(\d+)$/
     */
    public function castStringToNumber($string)
    {
        return intval($string);
    }
}
