<?php
namespace Source\Core;

/**
 * 
 */
use  Source\Core\Session;
use Source\Models\Permissao;
use Source\Models\UserAcess;


class Dispach 
{
	
	private $routes;
	private $route;
	private $httpMethod;
	private $namespace;
	private $separa;
    private $projectUrl;
    private $patch;
	private $group;	
	private $errorCod;

   public function __construct($projectUrl,$separador="::"){
      
   			$this->separa=$separador;
   			$this->projectUrl = (substr($projectUrl, "-1") == "/" ? substr($projectUrl, 0, -1) : $projectUrl);
             $this->patch = (filter_input(INPUT_GET, "route", FILTER_DEFAULT) ?? "/");
   			$this->httpMethod = $_SERVER['REQUEST_METHOD'];
   			
   }



   protected function addRoute(string $method, string $route, string $handler)
   {
        if ($route == "/") {
            $this->addRoute($method,"", $handler);
        }

        

        preg_match_all("~\{\s* ([a-zA-Z_][a-zA-Z0-9_-]*) \}~x", $route, $keys, PREG_SET_ORDER);
        $routeDiff = array_values(array_diff(explode("/", $this->patch), explode("/", $route)));
        
         $this->formSpoofing();
        $offset = ($this->group ? 1 : 0);
        foreach ($keys as $key) {
           $this->data[$key[1]]=($routeDiff[$offset++] ?? null);
        }

        $route = (!$this->group ? $route : "/{$this->group}{$route}");
        $data = $this->data;
        $namespace = $this->namespace; 
        $router = function () use ($method, $handler, $data, $route, $namespace) {
            return [
                "route" => $route,
                "method" => $method,
                "handler" => $this->handler($handler, $namespace),
                "action" => $this->action($handler),
                "data" => $data
            ];
        };

        $route = preg_replace('~{([^}]*)}~', "([^/]+)", $route);
        $this->routes[$method][$route] = $router();
       
   }


   public function namespace(?string $namespace){

   	return $this->namespace=$namespace;
   }


   public function dispatch(): bool
    {
        if (empty($this->routes) || empty($this->routes[$this->httpMethod])) {
            //$this->error = self::NOT_IMPLEMENTED;
            return false;
        }

        $this->route = null;       
        foreach ($this->routes[$this->httpMethod] as $key => $route) {
            if (preg_match("~^" . $key . "$~", $this->patch, $found)) {
                $this->route = $route;
            }
        }


       return $this->execute();
    }

    public function erro()
    {
        $res="";
		if($this->errorCod){
           $res=$this->errorCod;
            return $res;
        }

        return $res;
	}   

private function handler($handler, $namespace)
    {
        return (!is_string($handler) ? $handler : "{$namespace}\\" . explode($this->separa, $handler)[0]);
    }

    /**
     * @param $handler
     * @return null|string
     */
    private function action($handler): ?string
    {
        return (!is_string($handler) ?: (explode($this->separa, $handler)[1] ?? null));
    }



    /**
     * httpMethod form spoofing
     */
    protected function formSpoofing(): void
    {
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (!empty($post['_method']) && in_array($post['_method'], ["PUT", "PATCH", "DELETE"])) {
            $this->httpMethod = $post['_method'];
            $this->data = $post;

            unset($this->data["_method"]);
            return;
        }

        if ($this->httpMethod == "POST") {
            $this->data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

            unset($this->data["_method"]);
            return;
        }

        if (in_array($this->httpMethod, ["PUT", "PATCH", "DELETE"]) && !empty($_SERVER['CONTENT_LENGTH'])) {
            parse_str(file_get_contents('php://input', false, null, 0, $_SERVER['CONTENT_LENGTH']), $putPatch);
            $this->data = $putPatch;

            unset($this->data["_method"]);
            return;
        }

        $this->data = [];
        return;
    }



    protected function execute()
    {
       

       if ($this->route) {

        //adiciomar quando for uma funcao
        if (is_callable($this->route['handler'])) {
                call_user_func($this->route['handler'], ($this->route['data'] ?? []));
                return true;
            }


            $controller = $this->route['handler'];
            $method = $this->route['action'];

            if (!$this->Permission($controller,$method,$this->route)) {

            redirect("/sem-permissao");
                return false;
            }

               

            if (class_exists($controller)) {
                $newController = new $controller($this);
                if (method_exists($controller, $method)) {
                    $newController->$method(($this->route['data'] ?? []));
                    return true;
                }

                $this->errorCod="METHOD_NOT_ALLOWED";
                return false;
            }
          $this->errorCod="BAD_REQUEST";
                return false;
       }
        
  $this->errorCod="NOT_FOUND";
   return false;
	   
}


private function Permission($controller,$metodo,$routa)
{
    $sessao=new Session();  
    $da=(new Permissao());
    $td=explode("\\",$controller);
    $data=$da->select("classe='{$td[1]}' AND metodo='{$metodo}'")->fitch();

    if (isset($sessao->auth)) 
    {

        if ($sessao->auth->operador=="Operador") {

   
           if ($routa["method"]=="POST") {
            return true;
           }

             if ($da->listUrl($routa["route"])) {
                
                 return true;
             }

            

            if (!$data) {
                echo "Salvem as permissões no banco";
               return false;
            }

             $UserAcess=(new UserAcess())->select("id_modulo_acess='{$data->id_modulo_acess}' AND id_user='{$sessao->auth->id_pessoa}'")->fitch();
                
             if (!$UserAcess) {
                       echo " SEM PERMISSAO";
                       return false;
            }

        }
       
    }
   return true;
}

 }