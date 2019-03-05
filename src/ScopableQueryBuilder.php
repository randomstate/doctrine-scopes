<?php


namespace RandomState\DoctrineScopes;



use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr\From;
use Doctrine\ORM\QueryBuilder;

class ScopableQueryBuilder extends QueryBuilder
{
    /**
     * @var ScopeCollection
     */
    protected $scopes;

    public function __construct(EntityManagerInterface $em, ScopeCollection $scopes = null)
    {
        parent::__construct($em);
        $this->scopes = $scopes ?? new ScopeCollection();
    }

    public function getScopes()
    {
        return $this->scopes;
    }

    public function add($dqlPartName, $dqlPart, $append = false)
    {
        $part = parent::add($dqlPartName, $dqlPart, $append);

        if($dqlPartName === 'from') {
            foreach($this->scopes->enabled() as $scope) {
                $clause = $this->getFromClause($dqlPart);
                $scope->apply($this, $this->getEntityManager()->getClassMetadata($clause->getFrom()), $clause->getAlias());
            }

        }

        return $part;
    }

    protected function getFromClause($clause)
    {
        if (is_string($clause)) {
            $spacePos = strrpos($clause, ' ');
            $from     = substr($clause, 0, $spacePos);
            $alias    = substr($clause, $spacePos + 1);

            $clause = new From($from, $alias);
        }

        return $clause;
    }
}