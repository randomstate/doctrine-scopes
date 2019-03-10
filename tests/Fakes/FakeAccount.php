<?php


namespace RandomState\DoctrineScopes\Tests\Fakes;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class FakeAccount
 * @package RandomState\DoctrineScopes\Tests\fakes
 * @ORM\Entity
 */
class FakeAccount
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     */
    protected $id;
}