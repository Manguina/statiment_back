<?php
namespace Source\Core;

/**
 * 
 */
class Routa extends Dispach
{
	


   public function __construct($namespace,$separador="::"){
      
   			parent::__construct($namespace,$separador);
   			
   }

   public function get($url,$classe)
   {
   	$this->addRoute("GET",$url,$classe);
   }
	
    public function post($url,$classe)
   {
   	$this->addRoute("POST",$url,$classe);
   }
}