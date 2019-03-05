<?php

namespace RandomState\DoctrineScopes;

use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use RandomState\DoctrineScopes\ScopableQueryBuilder;

interface QueryBuilderScope
{
    /**
     * @param $queryBuilder
     * @param ClassMetadata $classMetadata
     * @param $alias
     */
    public function apply($queryBuilder, ClassMetadata $classMetadata, $alias);
}