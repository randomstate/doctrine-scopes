<?php


namespace RandomState\DoctrineScopes\Tests\Fakes;


use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\QueryBuilder;
use RandomState\DoctrineScopes\QueryBuilderScope;

class FakeEntityInAccountScope implements QueryBuilderScope
{
    public $account;

    public function __construct($account)
    {
        $this->account = $account;
    }

    /**
     * @param QueryBuilder $queryBuilder
     * @param ClassMetadata $classMetadata
     * @param $alias
     */
    public function apply($queryBuilder, ClassMetadata $classMetadata, $alias)
    {
        if($classMetadata->getReflectionClass()->getName() === FakeEntity::class){
            $queryBuilder->andWhere("$alias.account = 1");
        }
    }
}