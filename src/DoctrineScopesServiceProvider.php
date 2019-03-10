<?php


namespace RandomState\DoctrineScopes;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Illuminate\Support\ServiceProvider;

class DoctrineScopesServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(ScopeCollection::class, function() {
           return new ScopeCollection();
        });

        $this->app->extend(EntityManager::class, function($em) {
            return (new DecoratableEntityManager($em))
                ->setQueryBuilderFactory(function() {
                    return $this->app->make(ScopableQueryBuilder::class);
                })
                ->extendRepositoryFactory(function(EntityRepository $repository) use($em) {
                    return new ScopedEntityRepository($repository, $em);
                })
                ;
        });
    }
}