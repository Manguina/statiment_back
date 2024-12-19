<?php 



namespace Source\Core;

use Models\Generic;


class AcessControl extends Db

{

	
 
	private $session;

	function __construct()

	{

		parent::__construct("configuracao");

	}



	public function acessPainel()
	{

          $data=$this->select()->fitch();
          if ($data) {
          	
          	if ($data->estado=="Teste") {

          		if (base64_decode($data->restante)<=0) {
          				$this->session=["tipo"=>"redireciona","sms"=>"Aplicação não Lincenciada"];
          				return false;
               }
          		if ($data->data_cre!=date("Y-m-d")) {
          			$this->id_empresa=$data->id_empresa;
          			$this->restante=base64_encode(base64_decode($data->restante)-1);
          			$this->data_cre=date("Y-m-d");

          		   $this->update($this->safe(), "id_empresa=:id", "id={$data->id_empresa}");
          		}
          		
          		$dias=base64_decode($data->restante)-1;
          		 $this->session=["tipo"=>"messagem","sms"=>"Estas a utilizar um Lincença de Teste que será fechado daqui a {$dias} dias"];
          		return false;
          	}

              if (!$this->verify($data)) {
               
                return false;
              }



              return true;
          }

 	$this->session=["tipo"=>"redireciona","sms"=>"Aplicação não Lincenciada"];
   
	}

    
     public function addLincenca()
     {
   			$this->update($this->safe(), "id_empresa=:id", "id={$this->id_empresa}");

   			 if ($this->fail) {

             $this->message->error("Erro ao Emitir a Lincença")->render();
                return false;

             }

         return true;
     }

public function messagem()
{
   
   return $this->session;
}
     private function verify($data){

     	if (!$data->tempo) {
     		$this->session=["tipo"=>"redireciona","sms"=>"Aplicação não Lincenciada"];
     		return false;
     	}


     	$da=explode(",", base64_decode($data->tempo));

     	if ($da[0]!=$data->nome_empresa || $da[1]!=$data->nif) 
     	{
     		$this->session=["tipo"=>"redireciona","sms"=>"Aplicação não Lincenciada"];
     		return false;
     	}
          

          if ($data->data_cre!=date("Y-m-d")) {
          			$this->id_empresa=$data->id_empresa;
          			$this->restante=base64_encode($da[5]-base64_decode($data->restante));
          			$this->data_cre=date("Y-m-d");
          		   $this->update($this->safe(), "id_empresa=:id", "id={$data->id_empresa}");
          		}

     	return true;
     }



}