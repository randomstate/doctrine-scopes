<?php

namespace RandomState\DoctrineScopes;

use Doctrine\ORM\Mapping\ClassMetadata;

interface QueryBuilderScope
{
    /**
     * @param $queryBuilder
     * @param ClassMetadata $classMetadata
     * @param $alias
     */
    public function apply($queryBuilder, ClassMetadata $classMetadata, $alias);
}