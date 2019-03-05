<?php


namespace RandomState\DoctrineScopes;


use Doctrine\ORM\EntityManager;
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
                ;
        });
    }
}