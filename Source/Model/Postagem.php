<?php
namespace Source\Model;

use Source\Core\Db;
use Source\Core\Session;

class Postagem extends Db
{
	
	function __construct()
	{
		parent::__construct("posts",[]);
	}


	
	public function save()
    {
         if (!empty($this->id)) {
            
            $id=$this->id;
            $this->update($this->safe(), "id = :id", "id={$id}");
            if ($this->fail()) {
                $this->message->error("Erro ao Editar Postagem");
                return false;

            }

            return true;
         }


         if (empty($this->id)) {
            
             $nif=$this->find("title=:p","p={$this->title}")->fetch();

             if ($nif) {
                 $this->message->error("Ja existe uma Postagem  com este titulo");
                return false;
             }

              

            $id = $this->insert($this->safe());
            if ($this->fail()) {
                var_dump($this->fail());
                $this->message->error("Erro ao cadastrar");

                return false;

            }  

         }

          $this->dados = $this->findById("id",$id)->data();
        return true;
    }
}