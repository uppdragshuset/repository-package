<?php

namespace Uppdragshuset\AO\Repository\Eloquent;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Uppdragshuset\AO\Repository\Contracts\Repository;
use Uppdragshuset\AO\Repository\Contracts\Criteria;

abstract class BaseRepository implements Repository, Criteria
{
    use AuthorizesRequests;

    protected $model;
    protected $criteria;
    protected $skipCriteria;
    protected $fieldSearchable;

    public function __construct(Collection $collection) {
        $this->criteria = $collection;
        $this->makeModel();
        $this->boot();
    }

    public function all($columns = array('*')) {
        $this->applyCriteria();
        if(!$this->model instanceof \Illuminate\Database\Eloquent\Builder){
            $this->authorize('index', $this->model);
            $results = $this->model->all($columns);
        }
        else{
            $results = $this->model->get($columns);
        }
        return $results;
    }

    public function lists($value, $key = null) {
        $this->authorize('index', $this->model);
        $lists = $this->model->lists($value, $key);
        if(is_array($lists)) {
            return $lists;
        }
        return $lists->all();
    }

    public function paginate($perPage = 10, $columns = array('*')) {
        $this->applyCriteria();
        if(!$this->model instanceof \Illuminate\Database\Eloquent\Builder){
            $this->authorize('index', $this->model);
        }
        return $this->model->paginate($perPage, $columns);
    }

    public function create(array $data) {
        $this->authorize('store', $this->model);
        return $this->model->create($data);
    }

    public function update(array $data, $id, $attribute="id") {
        $this->authorize('update', $this->model->find($id));
        $this->model->where($attribute, '=', $id)->update($data);
        return $this->model->find($id);
    }

    public function delete($id) {
        $this->authorize('destroy', $this->find($id));
        return $this->model->destroy($id);
    }

    public function find($id, $columns = array('*')) {
        $this->applyCriteria();
        if(!$this->model instanceof \Illuminate\Database\Eloquent\Builder){
            $this->authorize('index', $this->model);
        }
        return $this->model->find($id, $columns);
    }

    public function findBy($attribute, $value, $columns = array('*')) {
        $this->applyCriteria();
        if(!$this->model instanceof \Illuminate\Database\Eloquent\Builder){
            $this->authorize('index', $this->model);
        }
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    public function findAllBy($attribute, $value, $columns = array('*')) {
        $this->applyCriteria();
        if(!$this->model instanceof \Illuminate\Database\Eloquent\Builder){
            $this->authorize('index', $this->model);
        }
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();
        if(!$this->model instanceof \Illuminate\Database\Eloquent\Builder){
            $this->authorize('index', $this->model);
        }
        $model = $this->model;
        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (! $or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (! $or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (! $or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (! $or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }
        return $model->get($columns);
    }

    public function with($relations)
    {
        $this->authorize('index', $this->model);
        $this->model = $this->model->with($relations);
        return $this;
    }

    public abstract function model();

    public function makeModel() {
        $model = app()->make($this->model());
        if (!$model instanceof Model)
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        return $this->model = $model;
    }

    public function boot()
    {
        //
    }

    public function skipCriteria($status = true){
        $this->skipCriteria = $status;
        return $this;
    }

    public function getCriteria() {
        return $this->criteria;
    }

    public function getByCriteria(BaseCriteria $criteria) {
        $this->model = $criteria->apply($this->model, $this);
        return $this;
    }

    public function pushCriteria(BaseCriteria $criteria)
    {
        $this->criteria->push($criteria);
        return $this;
    }

    public function applyCriteria() {
        if($this->skipCriteria === true)
            return $this;
        foreach($this->getCriteria() as $criteria) {
            if($criteria instanceof BaseCriteria)
                $this->model = $criteria->apply($this->model, $this);
        }
        return $this;
    }

    public function getFieldsSearchable()
    {
        return $this->fieldSearchable;
    }
}