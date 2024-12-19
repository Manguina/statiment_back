<?php 

namespace Source\Controller;

use Source\Core\View;
use Source\Core\Engine;
use Source\Support\Message;
use Source\Core\Session;

class Controller 
{   

    protected $view;
    protected $message;
    protected $session;
    protected $engine;
    protected $photoMessage;
    protected $photo;

    function __construct($theme)
	{
		$this->message=(new Message());
        $this->view=(new View($theme));
        $this->session=(new Session());
        $this->engine=(new Engine("casa"));
        date_default_timezone_set('Africa/Luanda');
	}


    protected function views($pag, array $data=[])
    {
    	$this->view->section($pag,$data);
    	$this->view->render();
    }

     protected function uploadFiles($files,$fotoDefault): bool
    {
        

        if ($files['name']=="") {
            
            $this->photo=file_get_contents($fotoDefault);
            return true;
        }

        if ($files['error']!=0) {
            
            $this->photoMessage="ERROR AO CARREGAR A IMAGEM, TAMANHO DEMAZIADO GRANDE";
            echo json_encode($sms);
            return false;
        }

         if ($files['type']!="image/gif" && $files['type']!="image/jpeg" && $files['type']!="image/png" && $files['type']!="image/jpg") {
                
                 $this->photoMessage="O SISTEMA SÓ PERMITE O CARREGAMENTOS DOS ARQUIVOS DO TIPO(PNG,JPEG,JPG)";
                 echo json_encode($sms);
                return false;
        }

        $this->photo=file_get_contents($files['tmp_name']);
        return true;
    }
}
