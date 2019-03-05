<?php


namespace RandomState\DoctrineScopes\Tests;




use Doctrine\ORM\EntityManager;
use RandomState\DoctrineScopes\DecoratableEntityManager;
use RandomState\DoctrineScopes\DoctrineScopesServiceProvider;
use RandomState\DoctrineScopes\ScopableQueryBuilder;
use RandomState\DoctrineScopes\ScopeCollection;

class LaravelTest extends TestCase
{
    /** @var DecoratableEntityManager */
    protected $entityManager;

    public function setUp()
    {
        parent::setUp();
        $this->app->register(DoctrineScopesServiceProvider::class);
        $this->entityManager = $this->app->make(EntityManager::class);
    }

    /**
     * @test
     */
    public function service_provider_correctly_wraps_entity_manager_and_query_builder()
    {
        $this->assertInstanceOf(DecoratableEntityManager::class, $this->entityManager);
    }

    /**
     * @test
     */
    public function scope_collection_is_always_given_as_singleton()
    {
        $this->assertInstanceOf(ScopableQueryBuilder::class, $qb = $this->entityManager->createQueryBuilder());
        $this->assertInstanceOf(ScopableQueryBuilder::class, $qb);

        /** @var ScopableQueryBuilder $qb */
        $this->assertInstanceOf(ScopeCollection::class, $scopes = $qb->getScopes());
        $this->assertSame($scopes, $this->app->make(ScopeCollection::class));
        $this->assertTrue($this->app->isShared(ScopeCollection::class));
    }
}