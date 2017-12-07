<?php namespace Moo\OneApi;

class Customer
{

    protected $data;

    /**
     * Customer constructor.
     *
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function __get($name)
    {
        if (!isset($this->data[$name])) {
           throw new \InvalidArgumentException($name . ' does not exist on the Customer object.');
        }

        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

}