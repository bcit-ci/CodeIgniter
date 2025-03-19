<?php

/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\ORM\Cache\Persister\Entity;

use Doctrine\ORM\Cache;
use Doctrine\ORM\Cache\Region;
use Doctrine\ORM\Cache\EntityCacheKey;
use Doctrine\ORM\Cache\CollectionCacheKey;
use Doctrine\ORM\Cache\TimestampCacheKey;
use Doctrine\ORM\Cache\QueryCacheKey;
use Doctrine\ORM\Cache\Persister\CachedPersister;
use Doctrine\ORM\Cache\CacheException;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\PersistentCollection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Persisters\Entity\EntityPersister;

use Doctrine\Common\Util\ClassUtils;
use Doctrine\Common\Collections\Criteria;

/**
 * @author Fabio B. Silva <fabio.bat.silva@gmail.com>
 * @since 2.5
 */
abstract class AbstractEntityPersister implements CachedEntityPersister
{
     /**
     * @var \Doctrine\ORM\UnitOfWork
     */
    protected $uow;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadataFactory
     */
    protected $metadataFactory;

    /**
     * @var \Doctrine\ORM\Persisters\Entity\EntityPersister
     */
    protected $persister;

    /**
     * @var \Doctrine\ORM\Mapping\ClassMetadata
     */
    protected $class;

     /**
     * @var array
     */
    protected $queuedCache = array();

    /**
     * @var \Doctrine\ORM\Cache\Region
     */
    protected $region;

    /**
     * @var \Doctrine\ORM\Cache\TimestampRegion
     */
    protected $timestampRegion;

    /**
     * @var \Doctrine\ORM\Cache\TimestampCacheKey
     */
    protected $timestampKey;

    /**
     * @var \Doctrine\ORM\Cache\EntityHydrator
     */
    protected $hydrator;

    /**
     * @var \Doctrine\ORM\Cache
     */
    protected $cache;

    /**
     * @var \Doctrine\ORM\Cache\Logging\CacheLogger
     */
    protected $cacheLogger;

    /**
     * @var string
     */
    protected $regionName;

    /**
     * Associations configured as FETCH_EAGER, as well as all inverse one-to-one associations.
     *
     * @var array
     */
    protected $joinedAssociations;

    /**
     * @param \Doctrine\ORM\Persisters\Entity\EntityPersister $persister The entity persister to cache.
     * @param \Doctrine\ORM\Cache\Region                      $region    The entity cache region.
     * @param \Doctrine\ORM\EntityManagerInterface            $em        The entity manager.
     * @param \Doctrine\ORM\Mapping\ClassMetadata             $class     The entity metadata.
     */
    public function __construct(EntityPersister $persister, Region $region, EntityManagerInterface $em, ClassMetadata $class)
    {
        $configuration  = $em->getConfiguration();
        $cacheConfig    = $configuration->getSecondLevelCacheConfiguration();
        $cacheFactory   = $cacheConfig->getCacheFactory();

        $this->class            = $class;
        $this->region           = $region;
        $this->persister        = $persister;
        $this->cache            = $em->getCache();
        $this->regionName       = $region->getName();
        $this->uow              = $em->getUnitOfWork();
        $this->metadataFactory  = $em->getMetadataFactory();
        $this->cacheLogger      = $cacheConfig->getCacheLogger();
        $this->timestampRegion  = $cacheFactory->getTimestampRegion();
        $this->hydrator         = $cacheFactory->buildEntityHydrator($em, $class);
        $this->timestampKey     = new TimestampCacheKey($this->class->getTableName());
    }

    /**
     * {@inheritdoc}
     */
    public function addInsert($entity)
    {
        $this->persister->addInsert($entity);
    }

    /**
     * {@inheritdoc}
     */
    public function getInserts()
    {
        return $this->persister->getInserts();
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectSQL($criteria, $assoc = null, $lockMode = null, $limit = null, $offset = null, array $orderBy = null)
    {
        return $this->persister->getSelectSQL($criteria, $assoc, $lockMode, $limit, $offset, $orderBy);
    }

    /**
     * {@inheritDoc}
     */
    public function getCountSQL($criteria = array())
    {
        return $this->persister->getCountSQL($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getInsertSQL()
    {
        return $this->persister->getInsertSQL();
    }

    /**
     * {@inheritdoc}
     */
    public function getResultSetMapping()
    {
        return $this->persister->getResultSetMapping();
    }

    /**
     * {@inheritdoc}
     */
    public function getSelectConditionStatementSQL($field, $value, $assoc = null, $comparison = null)
    {
        return $this->persister->getSelectConditionStatementSQL($field, $value, $assoc, $comparison);
    }

    /**
     * {@inheritdoc}
     */
    public function exists($entity, Criteria $extraConditions = null)
    {
        if (null === $extraConditions) {
            $key = new EntityCacheKey($this->class->rootEntityName, $this->class->getIdentifierValues($entity));

            if ($this->region->contains($key)) {
                return true;
            }
        }

        return $this->persister->exists($entity, $extraConditions);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheRegion()
    {
        return $this->region;
    }

    /**
     * @return \Doctrine\ORM\Cache\EntityHydrator
     */
    public function getEntityHydrator()
    {
        return $this->hydrator;
    }

    /**
     * {@inheritdoc}
     */
    public function storeEntityCache($entity, EntityCacheKey $key)
    {
        $class      = $this->class;
        $className  = ClassUtils::getClass($entity);

        if ($className !== $this->class->name) {
            $class = $this->metadataFactory->getMetadataFor($className);
        }

        if ($class->containsForeignIdentifier) {
            foreach ($class->associationMappings as $name => $assoc) {
                if (!empty($assoc['id']) && !isset($assoc['cache'])) {
                    throw CacheException::nonCacheableEntityAssociation($class->name, $name);
                }
            }
        }

        $entry  = $this->hydrator->buildCacheEntry($class, $key, $entity);
        $cached = $this->region->put($key, $entry);

        if ($this->cacheLogger && $cached) {
            $this->cacheLogger->entityCachePut($this->regionName, $key);
        }

        return $cached;
    }

    /**
     * @param object $entity
     */
    private function storeJoinedAssociations($entity)
    {
        if ($this->joinedAssociations === null) {
            $associations = array();

            foreach ($this->class->associationMappings as $name => $assoc) {
                if (isset($assoc['cache']) &&
                    ($assoc['type'] & ClassMetadata::TO_ONE) &&
                    ($assoc['fetch'] === ClassMetadata::FETCH_EAGER || ! $assoc['isOwningSide'])) {

                    $associations[] = $name;
                }
            }

            $this->joinedAssociations = $associations;
        }

        foreach ($this->joinedAssociations as $name) {
            $assoc       = $this->class->associationMappings[$name];
            $assocEntity = $this->class->getFieldValue($entity, $name);

            if ($assocEntity === null) {
                continue;
            }

            $assocId        = $this->uow->getEntityIdentifier($assocEntity);
            $assocKey       = new EntityCacheKey($assoc['targetEntity'], $assocId);
            $assocPersister = $this->uow->getEntityPersister($assoc['targetEntity']);

            $assocPersister->storeEntityCache($assocEntity, $assocKey);
        }
    }

    /**
     * Generates a string of currently query
     *
     * @param array $query
     * @param string $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     * @param integer $timestamp
     *
     * @return string
     */
    protected function getHash($query, $criteria, array $orderBy = null, $limit = null, $offset = null, $timestamp = null)
    {
        list($params) = ($criteria instanceof Criteria)
            ? $this->persister->expandCriteriaParameters($criteria)
            : $this->persister->expandParameters($criteria);

        return sha1($query . serialize($params) . serialize($orderBy) . $limit . $offset . $timestamp);
    }

    /**
     * {@inheritdoc}
     */
    public function expandParameters($criteria)
    {
        return $this->persister->expandParameters($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function expandCriteriaParameters(Criteria $criteria)
    {
        return $this->persister->expandCriteriaParameters($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function getClassMetadata()
    {
        return $this->persister->getClassMetadata();
    }

    /**
     * {@inheritdoc}
     */
    public function getManyToManyCollection(array $assoc, $sourceEntity, $offset = null, $limit = null)
    {
        return $this->persister->getManyToManyCollection($assoc, $sourceEntity, $offset, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getOneToManyCollection(array $assoc, $sourceEntity, $offset = null, $limit = null)
    {
        return $this->persister->getOneToManyCollection($assoc, $sourceEntity, $offset, $limit);
    }

    /**
     * {@inheritdoc}
     */
    public function getOwningTable($fieldName)
    {
        return $this->persister->getOwningTable($fieldName);
    }

    /**
     * {@inheritdoc}
     */
    public function executeInserts()
    {
        $this->queuedCache['insert'] = $this->persister->getInserts();

        return $this->persister->executeInserts();
    }

    /**
     * {@inheritdoc}
     */
    public function load(array $criteria, $entity = null, $assoc = null, array $hints = array(), $lockMode = null, $limit = null, array $orderBy = null)
    {
        if ($entity !== null || $assoc !== null || ! empty($hints) || $lockMode !== null) {
            return $this->persister->load($criteria, $entity, $assoc, $hints, $lockMode, $limit, $orderBy);
        }

        //handle only EntityRepository#findOneBy
        $timestamp  = $this->timestampRegion->get($this->timestampKey);
        $query      = $this->persister->getSelectSQL($criteria, null, null, $limit, null, $orderBy);
        $hash       = $this->getHash($query, $criteria, null, null, null, $timestamp ? $timestamp->time : null);
        $rsm        = $this->getResultSetMapping();
        $querykey   = new QueryCacheKey($hash, 0, Cache::MODE_NORMAL);
        $queryCache = $this->cache->getQueryCache($this->regionName);
        $result     = $queryCache->get($querykey, $rsm);

        if ($result !== null) {
            if ($this->cacheLogger) {
                $this->cacheLogger->queryCacheHit($this->regionName, $querykey);
            }

            return $result[0];
        }

        if (($result = $this->persister->load($criteria, $entity, $assoc, $hints, $lockMode, $limit, $orderBy)) === null) {
            return null;
        }

        $cached = $queryCache->put($querykey, $rsm, array($result));

        if ($this->cacheLogger) {
            if ($result) {
                $this->cacheLogger->queryCacheMiss($this->regionName, $querykey);
            }

            if ($cached) {
                $this->cacheLogger->queryCachePut($this->regionName, $querykey);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function loadAll(array $criteria = array(), array $orderBy = null, $limit = null, $offset = null)
    {
        $timestamp  = $this->timestampRegion->get($this->timestampKey);
        $query      = $this->persister->getSelectSQL($criteria, null, null, $limit, $offset, $orderBy);
        $hash       = $this->getHash($query, $criteria, null, null, null, $timestamp ? $timestamp->time : null);
        $rsm        = $this->getResultSetMapping();
        $querykey   = new QueryCacheKey($hash, 0, Cache::MODE_NORMAL);
        $queryCache = $this->cache->getQueryCache($this->regionName);
        $result     = $queryCache->get($querykey, $rsm);

        if ($result !== null) {
            if ($this->cacheLogger) {
                $this->cacheLogger->queryCacheHit($this->regionName, $querykey);
            }

            return $result;
        }

        $result = $this->persister->loadAll($criteria, $orderBy, $limit, $offset);
        $cached = $queryCache->put($querykey, $rsm, $result);

        if ($this->cacheLogger) {
            if ($result) {
                $this->cacheLogger->queryCacheMiss($this->regionName, $querykey);
            }

            if ($cached) {
                $this->cacheLogger->queryCachePut($this->regionName, $querykey);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function loadById(array $identifier, $entity = null)
    {
        $cacheKey   = new EntityCacheKey($this->class->rootEntityName, $identifier);
        $cacheEntry = $this->region->get($cacheKey);
        $class      = $this->class;

        if ($cacheEntry !== null) {
            if ($cacheEntry->class !== $this->class->name) {
                $class = $this->metadataFactory->getMetadataFor($cacheEntry->class);
            }

            if (($entity = $this->hydrator->loadCacheEntry($class, $cacheKey, $cacheEntry, $entity)) !== null) {
                if ($this->cacheLogger) {
                    $this->cacheLogger->entityCacheHit($this->regionName, $cacheKey);
                }

                return $entity;
            }
        }

        $entity = $this->persister->loadById($identifier, $entity);

        if ($entity === null) {
            return null;
        }

        $class      = $this->class;
        $className  = ClassUtils::getClass($entity);

        if ($className !== $this->class->name) {
            $class = $this->metadataFactory->getMetadataFor($className);
        }

        $cacheEntry = $this->hydrator->buildCacheEntry($class, $cacheKey, $entity);
        $cached     = $this->region->put($cacheKey, $cacheEntry);

        if ($cached && ($this->joinedAssociations === null || count($this->joinedAssociations) > 0)) {
            $this->storeJoinedAssociations($entity);
        }

        if ($this->cacheLogger) {
            if ($cached) {
                $this->cacheLogger->entityCachePut($this->regionName, $cacheKey);
            }

            $this->cacheLogger->entityCacheMiss($this->regionName, $cacheKey);
        }

        return $entity;
    }

    /**
     * {@inheritDoc}
     */
    public function count($criteria = array())
    {
        return $this->persister->count($criteria);
    }

    /**
     * {@inheritdoc}
     */
    public function loadCriteria(Criteria $criteria)
    {
        $query       = $this->persister->getSelectSQL($criteria);
        $timestamp   = $this->timestampRegion->get($this->timestampKey);
        $hash        = $this->getHash($query, $criteria, null, null, null, $timestamp ? $timestamp->time : null);
        $rsm         = $this->getResultSetMapping();
        $querykey    = new QueryCacheKey($hash, 0, Cache::MODE_NORMAL);
        $queryCache  = $this->cache->getQueryCache($this->regionName);
        $cacheResult = $queryCache->get($querykey, $rsm);

        if ($cacheResult !== null) {
            if ($this->cacheLogger) {
                $this->cacheLogger->queryCacheHit($this->regionName, $querykey);
            }

            return $cacheResult;
        }

        $result = $this->persister->loadCriteria($criteria);
        $cached = $queryCache->put($querykey, $rsm, $result);

        if ($this->cacheLogger) {
            if ($result) {
                $this->cacheLogger->queryCacheMiss($this->regionName, $querykey);
            }

            if ($cached) {
                $this->cacheLogger->queryCachePut($this->regionName, $querykey);
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function loadManyToManyCollection(array $assoc, $sourceEntity, PersistentCollection $coll)
    {
        $persister = $this->uow->getCollectionPersister($assoc);
        $hasCache  = ($persister instanceof CachedPersister);
        $key       = null;

        if ($hasCache) {
            $ownerId = $this->uow->getEntityIdentifier($coll->getOwner());
            $key     = new CollectionCacheKey($assoc['sourceEntity'], $assoc['fieldName'], $ownerId);
            $list    = $persister->loadCollectionCache($coll, $key);

            if ($list !== null) {
                if ($this->cacheLogger) {
                    $this->cacheLogger->collectionCacheHit($persister->getCacheRegion()->getName(), $key);
                }

                return $list;
            }
        }

        $list = $this->persister->loadManyToManyCollection($assoc, $sourceEntity, $coll);

        if ($hasCache) {
            $persister->storeCollectionCache($key, $list);

            if ($this->cacheLogger) {
                $this->cacheLogger->collectionCacheMiss($persister->getCacheRegion()->getName(), $key);
            }
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function loadOneToManyCollection(array $assoc, $sourceEntity, PersistentCollection $coll)
    {
        $persister = $this->uow->getCollectionPersister($assoc);
        $hasCache  = ($persister instanceof CachedPersister);

        if ($hasCache) {
            $ownerId = $this->uow->getEntityIdentifier($coll->getOwner());
            $key     = new CollectionCacheKey($assoc['sourceEntity'], $assoc['fieldName'], $ownerId);
            $list    = $persister->loadCollectionCache($coll, $key);

            if ($list !== null) {
                if ($this->cacheLogger) {
                    $this->cacheLogger->collectionCacheHit($persister->getCacheRegion()->getName(), $key);
                }

                return $list;
            }
        }

        $list = $this->persister->loadOneToManyCollection($assoc, $sourceEntity, $coll);

        if ($hasCache) {
            $persister->storeCollectionCache($key, $list);

            if ($this->cacheLogger) {
                $this->cacheLogger->collectionCacheMiss($persister->getCacheRegion()->getName(), $key);
            }
        }

        return $list;
    }

    /**
     * {@inheritdoc}
     */
    public function loadOneToOneEntity(array $assoc, $sourceEntity, array $identifier = array())
    {
        return $this->persister->loadOneToOneEntity($assoc, $sourceEntity, $identifier);
    }

    /**
     * {@inheritdoc}
     */
    public function lock(array $criteria, $lockMode)
    {
        $this->persister->lock($criteria, $lockMode);
    }

    /**
     * {@inheritdoc}
     */
    public function refresh(array $id, $entity, $lockMode = null)
    {
        $this->persister->refresh($id, $entity, $lockMode);
    }

}
