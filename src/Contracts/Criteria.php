<?php

namespace Uppdragshuset\AO\Repository\Contracts;

use Uppdragshuset\AO\Repository\Eloquent\BaseCriteria;

interface Criteria {
    /**
     * @param bool $status
     * @return $this
     */
    public function skipCriteria($status = true);
    /**
     * @return mixed
     */
    public function getCriteria();
    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function getByCriteria(BaseCriteria $criteria);
    /**
     * @param Criteria $criteria
     * @return $this
     */
    public function pushCriteria(BaseCriteria $criteria);
    /**
     * @return $this
     */
    public function  applyCriteria();
}