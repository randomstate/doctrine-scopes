<?php


namespace RandomState\DoctrineScopes;


use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

class ScopedEntityRepository implements ObjectRepository
{
    /**
     * @var EntityRepository
     */
    protected $entityRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    public function __construct(EntityRepository $entityRepository, EntityManagerInterface $entityManager)
    {
        $this->entityRepository = $entityRepository;
        $this->entityManager = $entityManager;
    }

    public function find($id)
    {
        $qb = $this->entityRepository->createQueryBuilder('e');

        $idColumns = $this->entityManager->getClassMetadata($this->getClassName())->getIdentifierColumnNames();
        $isComposite = count($idColumns) > 1;

        foreach($idColumns as $name) {
            $qb->andWhere("e.$name = :{$name}_id")
                ->setParameter("{$name}_id", $isComposite ? $idColumns[$name] : $id);
        }

        return $qb->getQuery()
            ->getOneOrNullResult();
    }

    public function findAll()
    {
        return $qb = $this->entityRepository->createQueryBuilder('e')
            ->getQuery()
            ->getResult();
    }

    public function findBy(array $criteria, ?array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->entityRepository->createQueryBuilder('e');

        foreach($criteria ?? [] as $key => $value) {
            if(is_array($value)) {
                $qb->andWhere($qb->expr()->in("e.$key", $value));
                continue;
            }

            $qb->andWhere($qb->expr()->eq("e.$key", $value));
        }

        foreach($orderBy ?? [] as $key => $sort) {
            $qb->addOrderBy("e.$key", $sort);
        }

        if($limit !== null) {
            $qb->setMaxResults($limit);
        }

        if($offset !== null) {
            $qb->setFirstResult($offset);
        }

        return $qb->getQuery()->getResult();
    }

    public function findOneBy(array $criteria)
    {
        return $this->findBy($criteria, null, 1);
    }

    public function getClassName()
    {
        return $this->entityRepository->getClassName();
    }
}