<?php


namespace RandomState\DoctrineScopes;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class DecoratableEntityManager implements EntityManagerInterface
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;
    protected $queryBuilderFactory;
    protected $afterRepositoryInstantiated = [];

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function setQueryBuilderFactory(\Closure $factory)
    {
        $this->queryBuilderFactory = $factory;

        return $this;
    }

    public function extendRepositoryFactory(\Closure $closure)
    {
        $this->afterRepositoryInstantiated[] = $closure;

        return $this;
    }

    public function getCache()
    {
        return $this->entityManager->getCache();
    }

    public function getConnection()
    {
        return $this->entityManager->getConnection();
    }

    public function getExpressionBuilder()
    {
        return $this->entityManager->getExpressionBuilder();
    }

    public function beginTransaction()
    {
        return $this->entityManager->beginTransaction();
    }

    public function transactional($func)
    {
        return $this->entityManager->transactional($func);
    }

    public function commit()
    {
        return $this->entityManager->commit();
    }

    public function rollback()
    {
        return $this->entityManager->rollback();
    }

    public function createQuery($dql = '')
    {
        return $this->entityManager->createQuery($dql);
    }

    public function createNamedQuery($name)
    {
        return $this->entityManager->createNamedQuery($name);
    }

    public function createNativeQuery($sql, ResultSetMapping $rsm)
    {
        return $this->entityManager->createNativeQuery($sql, $rsm);
    }

    public function createNamedNativeQuery($name)
    {
        return $this->entityManager->createNamedNativeQuery($name);
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder | ScopableQueryBuilder
     */
    public function createQueryBuilder()
    {
        return $this->queryBuilderFactory ? ($this->queryBuilderFactory)() : $this->entityManager->createQueryBuilder();
    }

    public function getReference($entityName, $id)
    {
        return $this->getReference($entityName, $id);
    }

    public function getPartialReference($entityName, $identifier)
    {
        return $this->entityManager->getPartialReference($entityName, $identifier);
    }

    public function close()
    {
        return $this->entityManager->close();
    }

    public function copy($entity, $deep = false)
    {
        return $this->entityManager->copy($entity, $deep);
    }

    public function lock($entity, $lockMode, $lockVersion = null)
    {
        return $this->entityManager->lock($entity, $lockMode, $lockVersion);
    }

    public function getEventManager()
    {
        return $this->entityManager->getEventManager();
    }

    public function getConfiguration()
    {
        return $this->entityManager->getConfiguration();
    }

    public function isOpen()
    {
        return $this->entityManager->isOpen();
    }

    public function getUnitOfWork()
    {
        return $this->entityManager->getUnitOfWork();
    }

    public function getHydrator($hydrationMode)
    {
        return $this->entityManager->getHydrator($hydrationMode);
    }

    public function newHydrator($hydrationMode)
    {
        return $this->entityManager->newHydrator($hydrationMode);
    }

    public function getProxyFactory()
    {
        return $this->entityManager->getProxyFactory();
    }

    public function getFilters()
    {
        return $this->entityManager->getFilters();
    }

    public function isFiltersStateClean()
    {
        return $this->entityManager->isFiltersStateClean();
    }

    public function hasFilters()
    {
        return $this->entityManager->hasFilters();
    }

    public function find($className, $id)
    {
        $idColumns = $this->getClassMetadata($className)->getIdentifierColumnNames();

        $qb = $this->createQueryBuilder()->select('e')->from($className, 'e');
        $isComposite = count($idColumns) > 1;

        foreach($idColumns as $name) {
            $qb->andWhere("e.$name = :{$name}_id")
                ->setParameter("{$name}_id", $isComposite ? $idColumns[$name] : $id);
        }

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    public function persist($object)
    {
        return $this->entityManager->persist($object);
    }

    public function remove($object)
    {
        return $this->entityManager->remove($object);
    }

    public function merge($object)
    {
        return $this->entityManager->merge($object);
    }

    public function clear($objectName = null)
    {
        return $this->entityManager->clear($objectName);
    }

    public function detach($object)
    {
        return $this->entityManager->detach($object);
    }

    public function refresh($object)
    {
        return $this->entityManager->refresh($object);
    }

    public function flush()
    {
        return $this->entityManager->flush();
    }

    public function getRepository($className)
    {
        $repo = $this->entityManager->getConfiguration()->getRepositoryFactory()->getRepository($this, $className);

        foreach($this->afterRepositoryInstantiated as $extension) {
            $repo = $extension($repo);
        }

        return $repo;
    }

    public function getClassMetadata($className)
    {
        return $this->entityManager->getClassMetadata($className);
    }

    public function getMetadataFactory()
    {
        return $this->entityManager->getMetadataFactory();
    }

    public function initializeObject($obj)
    {
        return $this->entityManager->initializeObject($obj);
    }

    public function contains($object)
    {
        return $this->entityManager->contains($object);
    }
}