<?php 

namespace App;
use Source\Controller\Controller; 
use Source\Model\Postagem;

class Api extends Controller
{
	private $postagem;
	function __construct()
	{
		header("Content-Type: application/json");
		header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST');
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
		parent::__construct("erros");
		$this->postagem=(new Postagem());

	}

	public function destque()
	{
        $post=$this->postagem->find("tipo='Destaque'")->order("id DESC")->fetch();
        $recomenda=$this->postagem->find("tipo='Recomendado'")->order("id DESC")->limit(10)->fetch(true);
        $normal=$this->postagem->find("tipo='Normal'")->order("id DESC")->limit(40)->fetch(true);
        
        if (!$post->data()) {
        	 $da=[];
        	return;
        }
        $da=get_object_vars($post->data());

        echo json_encode(["destaque"=>$da,"reco"=>$this->recomenda($recomenda),"normal"=>$this->normal($normal)]);
	}


	public function sigle($data)
	{

		 $id=$id = filter_var($data['id'], FILTER_VALIDATE_INT);
         $post=$this->postagem->find("id=:id","id={$id}")->fetch();
         $normal=(new Postagem())->find()->order("id DESC")->limit(5)->fetch(true);
          if (!$post->data()) {
        	 $da=[];
        	return;
        }
        $da=get_object_vars($post->data());
        echo json_encode(["post"=>$da,"lista"=>$this->recomenda($normal)]);
		
	}


	private function recomenda($data)
	{
		$retorn=[];
        if ($data) {
        	foreach ($data as $da) {
        		$retorn[]=get_object_vars($da->data());
        	}
        }

        return $retorn;
	}

	private function normal($data)
	{
		$retorn=[];
        if ($data) {
        	foreach ($data as $da) {
        		$retorn[]=get_object_vars($da->data());
        	}
        }

        return $retorn;
	}
}