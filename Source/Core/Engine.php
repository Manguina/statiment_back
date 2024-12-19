<?php

namespace Source\Core;

/**
 * 
 */

class Engine 
{
	
	private static $src;
	private static $thema;
	private  $extencao;
	private  $seo=[];
	private  $layout;
	public  $contant;
	public  array $data;
    private  array $section;
	private  string $sectionAtual;

	function __construct(string $thema, ?string $src=null)
	{
		self::$src=dirname(__FILE__,2)."/Public/{$src}";
		self::$thema = $thema;
		$this->extencao=".php";

	}

    private function load()
    {
        return !empty($this->contant) ? $this->contant : '';

    }

    private function extracts(string $view, array $data=[])
    {
        $this->layout=$view;
        $this->data=$data;
    }


   
    private function start($data){

        ob_start();

        $this->sectionAtual=$data;

    }

    private function end(){

        $this->section[$this->sectionAtual]=ob_get_contents();
         ob_end_clean();
    }

     private function section(string $data)
    {
        return $this->section[$data] ?? null;
    }


    public function insert(string $pagina, ?array $datas=[]): void
   {   
      $p=dirname(__FILE__,3)."/Public/{$pagina}.php";
        
        $da=extract($datas);
        if (!file_exists($p)) {
            echo "Not Found {$p}";
            return;
        }
 
        include($p);

        return;   
   }


	public function render(string $files, array $data=[])
    {
        $source=dirname(__FILE__,3)."/Public/{$files}.php";
        if (!file_exists($source)) {
            echo "View Not Found {$source}";
            return;
        }

        ob_start();

        extract($data);

        require($source);

        $content=ob_get_contents();

        ob_end_clean();

        if (!empty($this->layout)) {
           
           $this->contant=$content;
           $data=array_merge($this->data,$data);
           $layout= $this->layout;
           $this->layout=null;
           $this->render($layout,$data);

           return; 
        }

        echo $content;

    }
}