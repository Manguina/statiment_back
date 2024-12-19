<?php 

namespace App;
use Source\Controller\Controller; 
use Source\Model\User;

class Login extends Controller
{
	private $user;
	function __construct()
	{
		
		parent::__construct("erros");
		$this->user=(new User());
	}

	public function home()
	{
        $this->engine->render("user/login");
	}

	
	public function logar($data)
	{
		 if (empty($data["username"]) && $data["password"]) {
		 	echo json_encode(["error"=>"Campo Username ou Passord Vazio"]);
          	return;
		 }
          if (!$this->user->logar($data["username"],$data["password"])) {
          	echo json_encode(["error"=>$this->user->message()->getText()]);
          	return;
          }

          echo json_encode(["redirect"=>url("/admin")]);
	}
public function sair()
	{
		session_destroy();
		redirect("/");
	}

	
}