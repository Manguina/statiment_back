<?php
namespace Source\Core;

/**
 * 
 */
use Source\Core\Connect;
abstract class SaftConnect 
{
	
    private $pdo;
    private $query;
    private $where;
    private $grup;
    private $order;
    private $limit;
    private $offset;
    private $table;
    protected $dados;

    public function __construct(string $table) {
       $this->pdo=Connect::getInstance();
       $this->table=$table;
    }
    
    public function data()
    {
       return $this->dados;
    }

    public function select(string $fild="*")
     {
     	  $this->query="SELECT {$fild} FROM {$this->table}";

     	  return $this;
     }

      public function where(string $where)
    {
         $this->where = " WHERE {$where}";
        return $this;
    }

      public function order(string $columnOrder)
    {
        $this->order = " ORDER BY {$columnOrder}";
        return $this;
    }

     public function group(string $group)
    {
       	$this->grup = " GROUP BY {$group}";
        return $this;
    }

    /**
     * @param string $table
     * @return Model
     * */

    public function table( string $table)
    {
        
        $this->table=$table;
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


    // Read
    private function read(string $sql) {
    
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_CLASS,static::class);
    }

    /* CALL vender_roupa()
 METODO QUE PERMITE EXECUTAR A QUERY
*/
	public function fetch(bool $all=false){

		try {
			
			$smt=$this->pdo->prepare($this->query. $this->where. $this->grup . $this->order . $this->limit . $this->offset);
     			$smt->execute();
		 if (!$smt->rowCount()) {
                return null;
            }

            if ($all) {
            	$this->dados=$smt->fetchAll(\PDO::FETCH_CLASS,static::class);
            	 return $this->dados;
            }

            $this->dados=$smt->fetchObject(static::class);
            return $this->dados;
		} catch (\PDOException $exception) {
			$this->fail = $exception;
            return null;
		}

	}


    // Read All
    private function readAll() {
        $sql = "SELECT * FROM items";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update
    private function update($id, $name, $description) {
        $sql = "UPDATE items SET name = :name, description = :description WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        return $stmt->execute();
    }

    // Delete
    private function delete($id) {
        $sql = "DELETE FROM items WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

