<?php

namespace Doctrine\Tests\Common\Cache;

use Doctrine\Common\Cache\RedisCache;
use Doctrine\Common\Cache\Cache;

/**
 * @requires extension redis
 */
class RedisCacheTest extends CacheTest
{
    private $_redis;

    protected function setUp()
    {
        $this->_redis = new \Redis();
        $ok = @$this->_redis->connect('127.0.0.1');
        if (!$ok) {
            $this->markTestSkipped('Cannot connect to Redis.');
        }
    }

    public function testHitMissesStatsAreProvided()
    {
        $cache = $this->_getCacheDriver();
        $stats = $cache->getStats();

        $this->assertNotNull($stats[Cache::STATS_HITS]);
        $this->assertNotNull($stats[Cache::STATS_MISSES]);
    }

    public function testGetRedisReturnsInstanceOfRedis()
    {
        $this->assertInstanceOf('Redis', $this->_getCacheDriver()->getRedis());
    }

    /**
     * {@inheritDoc}
     */
    protected function _getCacheDriver()
    {
        $driver = new RedisCache();
        $driver->setRedis($this->_redis);
        return $driver;
    }
}
