<?php


namespace RandomState\DoctrineScopes\Tests;



use Doctrine\ORM\EntityManager;
use RandomState\DoctrineScopes\ScopableQueryBuilder;
use RandomState\DoctrineScopes\ScopeCollection;
use RandomState\DoctrineScopes\Tests\Fakes\FakeEntity;
use RandomState\DoctrineScopes\Tests\Fakes\FakeEntityInAccountScope;

class ScopableQueryBuilderTest extends TestCase
{
    /**
     * @test
     */
    public function can_use_scopable_query_builder_without_scopes()
    {
        $builder = new ScopableQueryBuilder($this->app->make(EntityManager::class));
        $builder->select('u.id')->from(FakeEntity::class, 'u');

        $this->assertEquals("SELECT u.id FROM RandomState\DoctrineScopes\Tests\Fakes\FakeEntity u", $builder->getQuery()->getDQL());
    }

    /**
     * @test
     */
    public function can_enable_scope_which_affects_query_builder()
    {
        $builder = new ScopableQueryBuilder($this->app->make(EntityManager::class), $scopes = new ScopeCollection());

        $scopes->add('scopeid', new FakeEntityInAccountScope(1));
        $this->assertFalse($scopes->isEnabled('scopeid'));
        $scopes->enable('scopeid');

        $this->assertTrue($scopes->isEnabled('scopeid'));
        $builder->select('u.id')->from(FakeEntity::class, 'u');

        $this->assertEquals('SELECT u.id FROM RandomState\DoctrineScopes\Tests\Fakes\FakeEntity u WHERE u.account = 1', $builder->getQuery()->getDQL());
    }

    /**
     * @test
     */
    public function can_disable_scope()
    {
        $builder = new ScopableQueryBuilder($this->app->make(EntityManager::class), $scopes = new ScopeCollection());

        $scopes->add('scopeid', new FakeEntityInAccountScope(1));
        $scopes->enable('scopeid');
        $scopes->disable('scopeid');

        $this->assertFalse($scopes->isEnabled('scopeid'));
        $builder->select('u.id')->from(FakeEntity::class, 'u');

        $this->assertEquals('SELECT u.id FROM RandomState\DoctrineScopes\Tests\Fakes\FakeEntity u', $builder->getQuery()->getDQL());
    }

    /**
     * @test
     */
    public function without_select()
    {
        $this->markTestIncomplete();


        /** @var EntityManager $em */
        $builder = new ScopableQueryBuilder($em = $this->app->make(EntityManager::class), $scopes = new ScopeCollection());

        $scopes->add('scopeid', new FakeEntityInAccountScope(1));
        $this->assertFalse($scopes->isEnabled('scopeid'));
        $scopes->enable('scopeid');

        $this->assertTrue($scopes->isEnabled('scopeid'));
        $builder->select('u.id')->from(FakeEntity::class, 'u');

        // entity persister
        // create scopedquerybuilder
        // select target alias and entity
        // remove select statement
        $builder->select('t0')
            ->from(FakeEntity::class, 't0');
        $parts = $builder->getDQLParts();
        $query = $builder->getQuery();
        dd($query->getSQL());
    }
}