<?php 

namespace App;
use Source\Controller\Controller; 
use Source\Model\User;

class UserController extends Controller
{
	private $user;
	function __construct()
	{
		    if (!User::userLogado()) {
            //$this->message->error("Efetue login para acessar ao sistema.");
            redirect("/");

         }
		parent::__construct("erros");
		$this->user=(new User());
	}

	public function home()
	{
        $this->engine->render("user/home",[
          "postagem"=>$this->user->find()->fetch(true)
        ]);
	}

	public function store($data)
	{
	 
		if (!isset($data["id"])) {
			$da= new \stdClass();
			$da->id="";
			$da->nome="";
			$da->username="";
			$da->tipo="";
		}else{
			$id=base64_decode($data["id"]);
			$da=$this->user->find("id=:id","id={$id}")->fetch();
		}
		$this->engine->render("user/cadastro",[
          "dados"=>$da
        ]);
	}




	

	public function save($data)
	{
       $da=$this->user;
      if (empty($data["nome"])) {
      	echo json_encode(["error"=>"Campo nome Vazio"]);
      	return;
      }

      if (empty($data["username"])) {
      	echo json_encode(["error"=>"Campo username Vazio"]);
      	return;
      }

      if (empty($data["password"])) {
      	echo json_encode(["error"=>"Campo password Vazio"]);
      	return;
      }


      if (!empty($data["id"])) {
      	 $da->id=$data["id"];
      }
       $da->nome=$data["nome"];
       $da->username=$data["username"];
       if (!empty($data["id"])) {
       	 if (!empty(data["password"])) {
       	 	 $da->password=$data["password"];
       	 }
       }else{
         $da->password=$data["password"];
       }
       $da->tipo=$data["tipo"];

       if (!$da->save()) {
       	 echo json_encode(["error"=>$da->message()->getText()]);
       	 return;
       }

       echo json_encode(["success"=>"Usuario Criado com Sucesso"]);
	}



}