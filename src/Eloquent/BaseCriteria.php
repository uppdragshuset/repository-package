<?php

namespace Uppdragshuset\AO\Repository\Eloquent;
use Uppdragshuset\AO\Repository\Contracts\Repository;

abstract class BaseCriteria {
    public abstract function apply($model, Repository $repository);
}