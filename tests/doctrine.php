<?php

use App\Account\Entities\AdvisorInvite;
use App\Account\Entities\Advisorship;
use App\Account\Entities\ClientInvite;
use App\Account\Entities\Invite;
use App\Account\Mapping\AdvisorInviteMapping;
use App\Account\Repositories\AdvisorshipRepository;
use App\Account\Repositories\ClientInviteRepository;
use App\Account\Repositories\InviteRepository;
use App\Billing\Entities\Plan;
use App\Billing\Entities\SubscriptionItem;
use App\Billing\Repositories\PlanRepository;
use App\Billing\Repositories\SubscriptionItemRepository;
use App\Common\DoctrineTypes\CarbonIntervalType;
use App\Common\DoctrineTypes\ChronosIntervalType;
use App\Common\DoctrineTypes\VatRateType;
use App\Common\Filters\TenantFilter;
use App\Identity\Entities\Circle;
use App\Identity\Entities\Organisation;
use App\Identity\Entities\User;
use App\Identity\Mapping\CircleMapping;
use App\Identity\Mapping\OrganisationMapping;
use App\Identity\Mapping\PermissionMapping;
use App\Identity\Mapping\UserMapping;
use App\Identity\Repositories\CircleRepository;
use App\Identity\Repositories\OrganisationRepository;
use App\Identity\Repositories\UserRepository;
use App\Projections\Entities\Event;
use App\Projections\Entities\LineItem;
use App\Projections\Entities\LineItems\Product;
use App\Projections\Entities\Projection;
use App\Projections\Entities\SectionComment;
use App\Projections\Mappings\Events\DiscreteTransactionMapping;
use App\Projections\Mappings\Events\TransactionMapping;
use App\Projections\Mappings\LineItemMapping;
use App\Projections\Mappings\LineItems\EmployeeMapping;
use App\Projections\Mappings\LineItems\ExpenseMapping;
use App\Projections\Mappings\LineItems\ProductMapping;
use App\Projections\Mappings\LineItems\SubscriptionMapping;
use App\Projections\Mappings\ProjectionMapping;
use App\Projections\Mappings\EventMapping;
use App\Projections\Repositories\EventRepository;
use App\Projections\Repositories\LineItemRepository;
use App\Projections\Repositories\ProductRepository;
use App\Projections\Repositories\ProjectionRepository;
use App\Projections\Repositories\SectionCommentRepository;
use DoctrineExtensions\Types\CarbonDateTimeType;
use LaravelDoctrine\ORM\Loggers\LaravelDebugbarLogger;

return [

    /*
    |--------------------------------------------------------------------------
    | Entity Mangers
    |--------------------------------------------------------------------------
    |
    | Configure your Entity Managers here. You can set a different connection
    | and driver per manager and configure events and filters. Change the
    | paths setting to the appropriate path and replace App namespace
    | by your own namespace.
    |
    | Available meta drivers: fluent|annotations|yaml|xml|config|static_php|php
    |
    | Available connections: mysql|oracle|pgsql|sqlite|sqlsrv
    | (Connections can be configured in the database config)
    |
    | --> Warning: Proxy auto generation should only be enabled in dev!
    |
    */
    'managers' => [
        'default' => [
            'dev' => env('APP_DEBUG', false),
            'meta' => env('DOCTRINE_METADATA', 'annotations'),
            'connection' => env('DB_CONNECTION', 'sqlite'),
            'namespaces' => [],
            'paths' => [
            ],
            'repository' => Doctrine\ORM\EntityRepository::class,
            'proxies' => [
                'namespace' => false,
                'path' => storage_path('proxies'),
                'auto_generate' => env('DOCTRINE_PROXY_AUTOGENERATE', false),
            ],
            /*
            |--------------------------------------------------------------------------
            | Doctrine events
            |--------------------------------------------------------------------------
            |
            | The listener array expects the key to be a Doctrine event
            | e.g. Doctrine\ORM\Events::onFlush
            |
            */
            'events' => [
                'listeners' => [],
                'subscribers' => [],
            ],
            'filters' => [
            ],
            /*
            |--------------------------------------------------------------------------
            | Doctrine mapping types
            |--------------------------------------------------------------------------
            |
            | Link a Database Type to a Local Doctrine Type
            |
            | Using 'enum' => 'string' is the same of:
            | $doctrineManager->extendAll(function (\Doctrine\ORM\Configuration $configuration,
            |         \Doctrine\DBAL\Connection $connection,
            |         \Doctrine\Common\EventManager $eventManager) {
            |     $connection->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
            | });
            |
            | References:
            | http://doctrine-orm.readthedocs.org/en/latest/cookbook/custom-mapping-types.html
            | http://doctrine-dbal.readthedocs.org/en/latest/reference/types.html#custom-mapping-types
            | http://doctrine-orm.readthedocs.org/en/latest/cookbook/advanced-field-value-conversion-using-custom-mapping-types.html
            | http://doctrine-orm.readthedocs.org/en/latest/reference/basic-mapping.html#reference-mapping-types
            | http://symfony.com/doc/current/cookbook/doctrine/dbal.html#registering-custom-mapping-types-in-the-schematool
            |--------------------------------------------------------------------------
            */
            'mapping_types' => [
                //'enum' => 'string'
            ],
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine Extensions
    |--------------------------------------------------------------------------
    |
    | Enable/disable Doctrine Extensions by adding or removing them from the list
    |
    | If you want to require custom extensions you will have to require
    | laravel-doctrine/extensions in your composer.json
    |
    */
    'extensions' => [
        //LaravelDoctrine\ORM\Extensions\TablePrefix\TablePrefixExtension::class,
        //LaravelDoctrine\Extensions\Timestamps\TimestampableExtension::class,
        //LaravelDoctrine\Extensions\SoftDeletes\SoftDeleteableExtension::class,
        //LaravelDoctrine\Extensions\Sluggable\SluggableExtension::class,
        //LaravelDoctrine\Extensions\Sortable\SortableExtension::class,
        //LaravelDoctrine\Extensions\Tree\TreeExtension::class,
        //LaravelDoctrine\Extensions\Loggable\LoggableExtension::class,
        //LaravelDoctrine\Extensions\Blameable\BlameableExtension::class,
        //LaravelDoctrine\Extensions\IpTraceable\IpTraceableExtension::class,
        //LaravelDoctrine\Extensions\Translatable\TranslatableExtension::class
    ],
    /*
    |--------------------------------------------------------------------------
    | Doctrine custom types
    |--------------------------------------------------------------------------
    |
    | Create a custom or override a Doctrine Type
    |--------------------------------------------------------------------------
    */
    'custom_types' => [
        'json' => LaravelDoctrine\ORM\Types\Json::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | DQL custom datetime functions
    |--------------------------------------------------------------------------
    */
    'custom_datetime_functions' => [],
    /*
    |--------------------------------------------------------------------------
    | DQL custom numeric functions
    |--------------------------------------------------------------------------
    */
    'custom_numeric_functions' => [],
    /*
    |--------------------------------------------------------------------------
    | DQL custom string functions
    |--------------------------------------------------------------------------
    */
    'custom_string_functions' => [],
    /*
    |--------------------------------------------------------------------------
    | Register custom hydrators
    |--------------------------------------------------------------------------
    */
    'custom_hydration_modes' => [
        // e.g. 'hydrationModeName' => MyHydrator::class,
    ],
    /*
    |--------------------------------------------------------------------------
    | Enable query logging with laravel file logging,
    | debugbar, clockwork or an own implementation.
    | Setting it to false, will disable logging
    |
    | Available:
    | - LaravelDoctrine\ORM\Loggers\LaravelDebugbarLogger
    | - LaravelDoctrine\ORM\Loggers\ClockworkLogger
    | - LaravelDoctrine\ORM\Loggers\FileLogger
    |--------------------------------------------------------------------------
    */
    'logger' => env('DOCTRINE_LOGGER', false),
    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | Configure meta-data, query and result caching here.
    | Optionally you can enable second level caching.
    |
    | Available: apc|array|file|memcached|redis|void
    |
    */
    'cache' => [
        'second_level' => false,
        'default' => env('DOCTRINE_CACHE', 'array'),
        'namespace' => null,
        'metadata' => [
            'driver' => env('DOCTRINE_METADATA_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => null,
        ],
        'query' => [
            'driver' => env('DOCTRINE_QUERY_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => null,
        ],
        'result' => [
            'driver' => env('DOCTRINE_RESULT_CACHE', env('DOCTRINE_CACHE', 'array')),
            'namespace' => null,
        ],
    ],
    /*
    |--------------------------------------------------------------------------
    | Gedmo extensions
    |--------------------------------------------------------------------------
    |
    | Settings for Gedmo extensions
    | If you want to use this you will have to require
    | laravel-doctrine/extensions in your composer.json
    |
    */
    'gedmo' => [
        'all_mappings' => false,
    ],
    /*
     |--------------------------------------------------------------------------
     | Validation
     |--------------------------------------------------------------------------
     |
     |  Enables the Doctrine Presence Verifier for Validation
     |
     */
    'doctrine_presence_verifier' => true,

    /*
     |--------------------------------------------------------------------------
     | Notifications
     |--------------------------------------------------------------------------
     |
     |  Doctrine notifications channel
     |
     */
    'notifications' => [
        'channel' => 'database',
    ],
];
