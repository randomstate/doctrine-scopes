<?php


namespace RandomState\DoctrineScopes\Tests\Fakes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class FakeEntity
 * @package RandomState\DoctrineScopes\Tests\fakes
 * @ORM\Entity
 */
class FakeEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id = 1;
}