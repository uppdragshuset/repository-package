<?php

namespace Uppdragshuset\AO\Repository\Criterias;

use Illuminate\Http\Request;
use Uppdragshuset\AO\Repository\Eloquent\BaseCriteria;
use Uppdragshuset\AO\Repository\Contracts\Repository;

class FieldRequestCriteria extends BaseCriteria
{
    /**
     * @var \Illuminate\Http\Request
     */
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Apply criteria in query repository
     *
     * @param $model
     * @param Repository $repository
     * @return mixed
     */

    public function apply($model, Repository $repository)
    {
        $fields = $repository->getFieldsSearchable();

        foreach($fields as $field) {
            if(!$this->request->get($field)) {
                continue;
            }
            $model = $model->where($field, $this->request->get($field));
        }

        return $model;

    }
}