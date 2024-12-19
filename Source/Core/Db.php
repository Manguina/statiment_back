<?php
namespace Source\Core;

/**
 * 
 */
use source\Support\Message;

abstract class Db
{
    protected $dado;

	protected  $table;
	protected  $query;
	protected  $param;
	protected  $fail;
	protected $message;
    protected $params;
    protected $tableConstum;

    protected $groupBy;

    /** @var string */
    protected $order;

    /** @var int */
    protected $limit;

    /** @var int */
    protected $offset;

      /** @var array */
    protected $required;


	function __construct(string $table, array $required=[])
	{
        $this->table=$table;
		$this->required=$required;
		$this->message = new Message();
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

/**
     * @return \PDOException
     */
    public function fail(): ?\PDOException
    {
        return $this->fail;
    }
/**
 * @return Message|Null
 * **/

 public function message(): ?Message
   {
        return $this->message;
   }

/*METODO DE INSERÇÃO DE DADOS*/
	protected function insert(array $data, ?string $tabela=null)
	{
		try {

            if (!empty($this->tableConstum)) {
              $this->table=$this->tableConstum;
           }

			$colunas=implode(",", array_keys($data));
			$value=":".implode(", :", array_keys($data));
            
            if ($tabela) {
            	
            	$stmt = Connect::getInstance()->prepare("INSERT INTO " . $tabela . " ({$colunas}) VALUES ({$value})");
            $stmt->execute($this->filter($data));
            return Connect::getInstance()->lastInsertId();
            }

			$stmt = Connect::getInstance()->prepare("INSERT INTO " . $this->table . " ({$colunas}) VALUES ({$value})");
            $stmt->execute($this->filter($data));
            return Connect::getInstance()->lastInsertId();
			
		} catch (\PDOException $exception) 
		{
			$this->fail = $exception;
            return null;
		}
	}

/*METODOS PARA O SELECT*/

/*
 METO JOIN PERMITE FAZER CONSULTA ATRAVEZ DE UM INNER JOIN
*/
    public function join(string $inner,?string $where=null,$param=null,string $columns="*")
    {
    	if ($where) {
    		$this->query="SELECT {$columns} FROM ".$this->table." {$inner} WHERE {$where}";
    		parse_str($param, $this->param);
    		return $this;
    	}
    	$this->query="SELECT {$columns} FROM ".$this->table." {$inner}";
    	return $this;
    }

/*
 METODO PERMITE FAZER CONSULTA SIMPLES 
*/
	public function find(?string $where=null, ?string $param=null , string $columns="*"){
           
           if (!empty($this->tableConstum)) {
              $this->table=$this->tableConstum;
           }

			if ($where) {
				$this->query="SELECT {$columns} FROM ".$this->table." WHERE {$where}";

				
                parse_str($param, $this->param);

                //var_dump($this->query,$this->param);
				return $this;
			}

			$this->query="SELECT {$columns} FROM ".$this->table;

			return $this;

	}
   
    /**
     * @param string $colId
     * @param int $id
     * @param string $columns
     * @return null|mixed|Model
     */
    public function findById(string $colId, int $id, string $columns = "*"): ?Db
    {
        $find = $this->find("{$colId}= :id", "id={$id}", $columns);
        return $find->fetch();
    }


    public function order(string $columnOrder)
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

     public function group(string $group)
    {
        $this->groupBy = " GROUP BY {$group}";
        return $this;
    }

    /**
     * @param string $table
     * @return Model
     * */

    public function tableConst( string $table)
    {
        $this->tableConstum=$table;
        return $this;
    }

    /**
     * @param int $limit
     * @return Model
     */
    public function limit(int $limit)
    {
        $this->limit = " LIMIT {$limit}";
        return $this;
    }

    /**
     * @param int $offset
     * @return Model
     */
    public function offset(int $offset)
    {
        $this->offset = " OFFSET {$offset}";
        return $this;
    }

/*TRAZER UM PROCEDURE*/
protected function proceNoew($nome, array $data){
    try {

           $colunas=implode(",", array_keys($data));
            $value=":".implode(", :", array_keys($data));
            $smt=Connect::getInstance()->prepare("CALL {$nome}({$value})");
            
            $smt->execute($this->filter($data));
            
             $smt->fetchAll(\PDO::FETCH_CLASS);
             
        return true;
            
        
    } catch(\PDOException $exception){
        $this->fail = $exception;
            return null;
    }
}

/* CALL vender_roupa()
 METODO QUE PERMITE EXECUTAR A QUERY
*/
	public function fetch(bool $all=false){

		try {
			
			$smt=Connect::getInstance()->prepare($this->query. $this->groupBy . $this->order . $this->limit . $this->offset);
		$smt->execute($this->param);
		 if (!$smt->rowCount()) {
                return null;
            }

            if ($all) {
            	 return $smt->fetchAll(\PDO::FETCH_CLASS,static::class);
            }
        return $smt->fetchObject(static::class);
		} catch (\PDOException $exception) {
			$this->fail = $exception;
            return null;
		}

	}

/*
*METODO QUE CONTA AS LINHAS DO BANCO DE DADOS
*/
/**
     * @param string $key
     * @return int
     */

public function count(string $key = "id"): int
    {
        $stmt = Connect::getInstance()->prepare($this->query);
        $stmt->execute($this->params);
        return $stmt->rowCount();
    }

/*metodo para apagar os dados*/

public function delete(string $key, string $value): bool
    {

        if (!empty($this->tableConstum)) {
              $this->table=$this->tableConstum;
           }

        try {
            $stmt = Connect::getInstance()->prepare("DELETE FROM " . $this->table . " WHERE {$key} = :key");
            $stmt->bindValue("key", $value, \PDO::PARAM_STR);
            $stmt->execute();

           
            return true;
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return false;
        }
    }

    /*METODO PARA FAZER UPDATE DOS DADOS*/


 /**
     * @param array $data
     * @param string $terms
     * @param string $params
     * @return int|null
     * 
**/

protected function update(array $data, string $terms, string $params): ?int
    {
        try {

            if (!empty($this->tableConstum)) {
              $this->table=$this->tableConstum;
           }

            $dateSet = [];
            foreach ($data as $bind => $value) {
                $dateSet[] = "{$bind} = :{$bind}";
            }
            $dateSet = implode(", ", $dateSet);
            parse_str($params, $params);

            $stmt = Connect::getInstance()->prepare("UPDATE " . $this->table . " SET {$dateSet} WHERE {$terms}");
            $stmt->execute($this->filter(array_merge($data, $params)));
            return ($stmt->rowCount() ?? 1);
        } catch (\PDOException $exception) {
            $this->fail = $exception;
            return null;
        }
    }

/*METODO QUE  TRATA OS DADOS A SEREM INSERIDOS NO BANCO*/

/**
 * @return array|null
 * 
 * 
 * **/

protected function safe(): ?array
    {
        $safe = (array)$this->dado;
        return $safe;
    }

/*METO DE FILTRAR DADOS QUE VAI SER INSERIDO NO BANCO*/
	private function filter(array $data): ?array
    {
        $filter = [];
        foreach ($data as $key => $value) {
            $filter[$key] = (is_null($value) ? null : filter_var($value, FILTER_DEFAULT));
        }
        return $filter;
    }

    protected function required(): bool
    {
        $data = (array)$this->data();
        foreach ($this->required as $field) {
            if (empty($data[$field])) {
                return false;
            }
        }
        return true;
    }
}

