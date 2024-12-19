<?php 

namespace Source\Core;
use Source\Core\Connect;


/**
 * 
 */
class Query 
{
	 private $pdo,$con,$sql;
   
     public $campos=[]; 
     public $tabela=""; 
     public $valores=[]; 
     public $param_array=[]; 
     public $parametro_join;
     public $dados;
     public $param_array_update=[];
     public $limite=50;
     public $todos_ou_um=0;
     public $parametro_Onde="";
            private $data;
	function __construct()
	{
		$this->pdo = Connect::getInstance();
		$this->con=$this->pdo;
	}


	














//=================salvacao generica================
    public function campos(array $dados) 
{

            $dateSet = [];
            foreach ($dados as $items) {
                $dateSet[] = $items;
            }

            return $dateSet = implode(", ", $dateSet);
         
}
public function valores(array $dados) 
{

    
            $dateSet = [];
            foreach ($dados as $items) {
                $dateSet[] = $items;
            }

            return $dateSet = implode(", ", $dateSet);

       
           
}

public function save_new(): int//saved
    {
         

                
$cadastro = "INSERT INTO  $this->tabela  (".$this->campos($this->campos).") VALUES (".$this->valores( $this->valores).")";            


        $result=$this->con->prepare($cadastro);

        $result->execute($this->param_array);
        
     if ($result) {

                return $this->pdo->lastInsertId();


     }else{

         $res=0;

     }
        
    }
     public function update_new(){//update
     
     
            $cadastro = "UPDATE $this->tabela SET  ".$this->campos($this->param_array_update)." WHERE ".$this->parametro_Onde." ";  


        $result=$this->con->prepare($cadastro);


        $result->execute($this->param_array);


     if ($result) {

       $res=1;

     }else{

         $res=0;

     }
        return $res;
    }
 
    public function selectAll_onde(){
     

     try {
         
        $this->sql="SELECT $this->parametro_join FROM $this->tabela WHERE ".$this->parametro_Onde." LIMIT $this->limite";   
   
        

        $result=$this->con->prepare($this->sql);
        
        $result->execute();
      if ($this->todos_ou_um=1) {
           
         $this->data=$result->fetchAll(\PDO::FETCH_ASSOC);

        }else if ($this->todos_ou_um=2) {
        
        $this->data=$result->fetch(\PDO::FETCH_ASSOC);

        } 
        
     } catch (\Throwable $e) {
         
     }
     
     return  $this->data;
        
    }
     public function selectAll_sem_onde(){
     

     try {
         
        $this->sql="SELECT $this->parametro_join FROM $this->tabela  LIMIT $this->limite";   
        $data="";

        $result=$this->con->prepare($this->sql);

        $result->execute();
        if ($this->todos_ou_um=1) {
             
           $this->data=$result->fetchAll(\PDO::FETCH_ASSOC);
  
          }else if ($this->todos_ou_um=2) {
          
          $this->data=$result->fetch(\PDO::FETCH_ASSOC);
  
          } 
          
       } catch (\Throwable $e) {
           
       }
       
       return  $this->data;
    }

     

//=================fim salvacao generica================

    public function update(string $sql, array $dados=null){
     
     
        $this->sql=$sql;
        $this->dados=$dados;
     
        $result=$this->con->prepare($this->sql);
        $result->execute($this->dados);
       
     if ($result) {

       $res=1;

     }else{

         $res=0;

     }
        return $res;
    }


}