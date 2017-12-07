<?php namespace Moo\Traits;

trait Findable {
    /**
     * @param string $uuid
     * @return mixed
     */
    function findByUuid($uuid)
    {
        return $this->model->whereUuid($uuid)->first();
    }

    /**
     * @param $id
     * @return mixed
     */
    function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * @param $includes
     * @return array
     */
    protected function relationshipsToLoad($includes)
    {
        $keys = [];

        if (property_exists($this,'possibleRelationships'))
        {
            $keys = array_keys(array_intersect($this->possibleRelationships,$includes));

            array_walk($keys,function(&$item){
                $item = camel_case($item);
            });

        }

        return $keys;
    }

    /**
     * @param array $filters
     * @param null  $paginate
     * @param array $orderBy
     * @param array $includes
     * @return mixed
     */
    function all(array $filters, $paginate = NULL, array $orderBy = ['id','asc'], array $includes = [])
    {
        $eagerLoad = $this->relationshipsToLoad($includes);

        $query = $this->model->with($includes)->orderBy($orderBy[0],$orderBy[1]);

        if(method_exists($this,'applyFilters'))
        {
            $query = $this->applyFilters($query,$filters);
        }

        if ($paginate !== NULL && is_numeric($paginate))
        {
            return $query->paginate($paginate);
        }
        else
        {
            return $query->get();
        }
    }

    /**
     * @param $query
     * @param $filters
     * @param $column
     * @return mixed
     */
    public function applyFilter($query,$filters,$column)
    {
        if(isset($filters[$column]) && strlen($filters[$column]) != 0)
        {
            $query->where($column,$filters[$column]);
        }

        return $query;
    }
}
