<?php
namespace Source\Model;

use Source\Core\Db;
use Source\Core\Session;

class User extends Db
{
	
	function __construct()
	{
		parent::__construct("users",[]);
	}

public static function userLogado()
    {
        $session = new Session();
        if (!$session->has("user")) {
            return null;
        }
        
    return $session->user->data();
        
       
    }


       public function logar(string $username, string $password) 
    {

            if (empty($username) || empty($password)) {
                
                 $this->message->error("Campo Usuario ou Senha Esta Vazio");
                

                return false;
            }

            $user=$this->find("username=:p","p={$username}")->fetch();


            if (!$user || !password_verify($password,$user->password)) {

                $this->message->error("Usuario ou Senha Invalído")->render();
                return false;
            }
        
        $this->dado=$this->find("id=:p","p={$user->id}")->fetch();
        $session=(new Session());
        $session->set("user",$this->data());
         return true;
    }

	
	public function save()
    {
        if (!is_passwd($this->password)) {
            $min = CONF_PASSWD_MIN_LEN;
            $max = CONF_PASSWD_MAX_LEN;
            $this->message->warning("A senha deve ter entre {$min} e {$max} caracteres");
            return false;

        } else {
            $this->password = passwd($this->password);
        }

         if (!empty($this->id)) {
            
            $id=$this->id;
            $this->update($this->safe(), "id = :id", "id={$id}");
            if ($this->fail()) {
                $this->message->error("Erro ao Editar Utilizador");
                return false;

            }

            return true;
         }


         if (empty($this->id)) {
            
             $nif=$this->find("username=:p","p={$this->username}")->fetch();

             if ($nif) {
                 $this->message->error("Ja existe um utlizador com esse username");
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