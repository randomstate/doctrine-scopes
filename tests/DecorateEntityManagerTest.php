<?php


namespace RandomState\DoctrineScopes\Tests;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use RandomState\DoctrineScopes\DecoratableEntityManager;
use RandomState\DoctrineScopes\Tests\fakes\FakeDecoratableQueryBuilder;

class DecorateEntityManagerTest extends TestCase
{
    /**
     * @test
     */
    public function can_inject_custom_query_builder_factory()
    {
        $em = $this->app->make(EntityManager::class);
        $decorated = new DecoratableEntityManager($em);

        $this->assertInstanceOf(QueryBuilder::class, $decorated->createQueryBuilder());

        $decorated->setQueryBuilderFactory(function() {
           return new FakeDecoratableQueryBuilder();
        });

        $this->assertInstanceOf(FakeDecoratableQueryBuilder::class, $decorated->createQueryBuilder());
    }
}