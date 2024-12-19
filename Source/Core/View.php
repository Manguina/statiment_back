<?php

namespace Source\Core;

/**
 * 
 */

class View 
{
	
	private static $src;
	private static $thema;
	private  $extencao;
	private  $seo=[];
	private  $dados;
	public  $pag;
	public  $geral;
	private  $permissao;

	function __construct(string $thema, ?string $src=null)
	{
		self::$src=__DIR__."/../../Public/".$src;
		self::$thema = $thema;
		$this->extencao=".php";
       ///var_dump($this->permissao->accessMenu());

	}

	private function template()
	{
		$url=self::$src.self::$thema.$this->extencao;
		if (file_exists($url)) {
			ob_start();
			include_once($url);
			return;
		}

		echo "NENHUMA TEMA FOI SELECIONADO";
	}
    
    public function seo(array $data) 
    {
    	$this->seo=(object) $data;
    	return; $this;
    }

    public function Geral($data){

    	$this->geral=(object)$data;

    	return $this;
    }

    private function setAddCodPage($code): ?string
    {
    	  $this->setcodname=$code;
    	 return $this->setcodname;

    }

       /*CODIGOS PARA RENDERIZAR OS SCRIPTS NA PAGINA*/

    public function scriptInicio($code)
    {
    	 $this->setcodname=$code;
         //opcache_reset();
    	 ob_start();
    	 return $this->setcodname;
    }

    public function scriptFim()
    {
    	 $this->getcod=[ob_get_clean()];
    	 return $this->getcod;
    }
    /*FIM CODIGOS PARA RENDERIZAR OS SCRIPTS NA PAGINA*/

    private function setCod(string $cod): ?array
    {
         
         $this->getcod=[$this->setcodname=>$cod];
         
         return  $this->getcod; 
    } 

    public function cod(string $data): ?string
   {
          
          $val="";

          if (!isset($this->getcod)) {
          	return $val;
          }

          foreach ($this->getcod as $key => $value) {
          	if ($key==$data) {
          		 $val=$value;
          	}
          }
             
           
         return $this->getcod[0];
       
   }

    public function addPage(string $page)
    {
    	$p=self::$src.$page.$this->extencao;
    	$data=(object)$this->dados;

    	$da=extract($this->dados);
    	
    	
    	if (empty($page)) {
    		echo "A SESSÃO  SELECIONADA ESTA VAZIA";
    		return;
    	}

    	if (!file_exists($p)) {
    		echo "ERRO FATAL NO SISTEMA PAGINA NÃO EXISTE";
    		return;
    	}
 
    	include($p);

    	return;
    }

    /**
    * função que renderiza trexos de codigos html ex. tabelas, listas, formularios etc
    * @param string $pagina
    * @param array $data|null
    * @return string 
    * **/

   public function v(string $pagina, ?array $datas=[]): ?string
   {   
       $html="";
       $p=self::$src.$pagina.$this->extencao;
     
     $da=extract($datas);
    
     if (!file_exists($p)) {
          $html="ERRO FATAL NO SISTEMA PAGINA NÃO EXISTE";
          return $html;
     }
    //  opcache_reset();
     ob_start();
       include($p);
     $html=[ob_get_clean()];

     return  $html[0];   
   }

    /*Funcão que adiciona pagina dentro da outra pagina ja existente*/

   public function insert(string $pagina, ?array $datas=[]): void
   {   
   	  $p=self::$src.$pagina.$this->extencao;
    	
    	$da=extract($datas);
    	if (!file_exists($p)) {
    		echo "ERRO FATAL NO SISTEMA PAGINA NÃO EXISTE";
    		return;
    	}
 
    	include($p);

    	return;   
   }

    public function section(string $page, $data=[])
    {
    	$this->pag=$page;
    	$this->dados=$data;
    }
	public function render()
	{
		$this->template();
	}
}