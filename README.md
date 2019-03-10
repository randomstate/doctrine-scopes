# Doctrine Scopes

One of the most important tasks in modern apps (particularly multi-tenant SaaS apps) is to ensure data isolation between your customers.
The 'best practice' for this is to filter all your queries at the global level.

Laravel Eloquent provides it.
Doctrine2 didn't.

This package fixes that 👌.

## Getting Started

`composer require randomstate/doctrine-scopes`

## Usage

Where you would normally create your entity manager, wrap it in a decoratable one and inject a scoped query builder factory closure.

```php
$scopes = new ScopeCollection();
$scope->add('myscope', new MyScope());
$scope->enable('myscope');

// Replace query builders with scopable ones
$em = new DecoratableEntityManager(new EntityManager(...));
$em->setQueryBuilderFactory(function() use($em, $scopes) {
    return new ScopableQueryBuilder($em, $scopes);
});

// Wrap repositories so that they are scoped
$em->extendRepositoryFactory(function(EntityRepository $repository) use($em) {
    return new ScopedEntityRepository($repository, $em);
})

$em->find(MyClass::class, 1); // this query is now scoped by whatever you have in MyScope@apply 🎉
```

### Laravel

For laravel users, this is easier:

Add `RandomState\DoctrineScopes\DoctrineScopesServiceProvider::class` to your providers list in your `config/app.php` file.

In the boot method of a service provider of your choice (e.g. `AppServiceProvider`):
```php

public function boot() {
    $this->app->extend(RandomState\DoctrineScopes\ScopeCollection::class, function($scopes) {
        $scopes->add('myscope', new MyScope);
        $scopes->enable('myscope');
        
        return $scopes;
    });      
}

```