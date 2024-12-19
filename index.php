<?php

ob_start();

require __DIR__ . "/vendor/autoload.php";

use CoffeeCode\Router\Router;


$router = new Router(url());

$router->namespace("App");
$router->get("/", "Login:home");
$router->get("/sair", "Login:sair");
$router->post("/entrar", "Login:logar");

$router->group("admin")->namespace("App");
$router->get("/", "PostController:home");
$router->get("/listar", "PostController:listar");
$router->get("/add/{id}", "PostController:store");
$router->get("/add", "PostController:store");
$router->get("/usuario", "UserController:home");
$router->get("/usuario/add", "UserController:store");
$router->get("/usuario/add/{id}", "UserController:store");
$router->post("/salvar", "PostController:save");
$router->post("/filtrar", "PostController:filtrar");
$router->post("/usuario/salvar", "UserController:save");



$router->group("api")->namespace("App");
$router->get("/", "Api:destque");
$router->get("/single/{id}", "Api:sigle");
$router->get("/salvar", "PostController:save");


/**rotas de erros**/
$router->group("error")->namespace("App");
$router->get("/{errcode}", "Error:notFound");

/**
 * This method executes the routes
 */
$router->dispatch();

/*
 * Redirect all errors
 */
if ($router->error()) {
   //redirect("/error/{$router->error()}");
   var_dump($router->error());
}

ob_end_flush();