<?php


namespace RandomState\DoctrineScopes;


class ScopeCollection
{
    protected $scopes = [];
    protected $enabledScopes = [];

    public function add($id, $scope)
    {
        $this->scopes[$id] = $scope;

        return $this;
    }

    public function enable($id)
    {
        $this->enabledScopes[$id] = $this->scopes[$id];

        return $this;
    }

    public function enabled()
    {
        return array_values($this->enabledScopes);
    }

    public function isEnabled($id)
    {
        return isset($this->enabledScopes[$id]);
    }

    public function disable($id)
    {
        unset($this->enabledScopes[$id]);

        return $this;
    }
}