<?php


namespace RandomState\DoctrineScopes\Tests;



use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\SchemaTool;
use LaravelDoctrine\ORM\Console\SchemaUpdateCommand;
use RandomState\DoctrineScopes\DecoratableEntityManager;
use RandomState\DoctrineScopes\DoctrineScopesServiceProvider;
use RandomState\DoctrineScopes\ScopeCollection;
use RandomState\DoctrineScopes\Tests\Fakes\FakeEntity;
use RandomState\DoctrineScopes\Tests\Fakes\FakeEntityInAccountScope;

class BaseRepositoryMethodsTest extends TestCase
{
    /** @var EntityRepository */
    private $repository;

    /** @var DecoratableEntityManager */
    private $entityManager;

    /** @var DebugStack */
    private $sqlLogger;

    /** @var ScopeCollection */
    private $scopes;

    public function setUp()
    {
        parent::setUp();
        $this->app->register(DoctrineScopesServiceProvider::class);
        $this->entityManager = $this->app->make(EntityManager::class);
        $this->scopes = $this->app->make(ScopeCollection::class);
        $this->repository = $this->entityManager->getRepository(FakeEntity::class);
        $this->sqlLogger = new DebugStack();
        $this->entityManager->getConfiguration()->setSQLLogger($this->sqlLogger);

        // Run sqlite migrations
        $tool = new SchemaTool($this->entityManager);
        $tool->updateSchema(
            $this->entityManager->getMetadataFactory()->getAllMetadata(),
            false
        );

        $this->scopes->add('account', new FakeEntityInAccountScope(1));
        $this->scopes->enable('account');
    }

    /**
     * @test
     */
    public function it_filters_find_by_id() 
    {
        $this->repository->find(1);

        $this->assertContains('account_id = 1', $this->lastQuery());
    }
    
    /**
     * @test
     */
    public function it_filters_find_by_criteria()
    {
        $this->repository->findBy(['id' => '2']);

        $this->assertContains('account_id = 1', $this->lastQuery());
    }

    /**
     * @test
     */
    public function it_scopes_find_one_by_criteria()
    {
        $this->repository->findOneBy(['id' => '2']);

        $this->assertContains('account_id = 1', $this->lastQuery());
    }

    protected function lastQuery()
    {
        return $this->sqlLogger->queries[$this->sqlLogger->currentQuery]['sql'];
    }
}