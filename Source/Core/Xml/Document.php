<?php
namespace Source\Core\Xml;

class Document
{
    protected $dado;

	protected  $table;
	protected  $query;
	protected  $param;
	protected  $fail;
	protected $message;
    protected $params;

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;


	function __construct()
	{
		
		
	}


  public function __set($name, $value)
    {
        if (empty($this->dado)) {
            $this->dado = new \stdClass();
        }

        $this->dado->$name = $value;
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->dado->$name);
    }

    /**
     * @param $name
     * @return null
     */
    public function __get($name)
    {
        return ($this->dado->$name ?? null);
    }

    /**
     * @return null|object
     */
    public function data()
    {
        return $this->dado;
    }

 
}