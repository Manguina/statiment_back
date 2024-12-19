<?php

/*url*/

function url(string $path = null): string
{

   
        if ($path) {
            return CONF_URL_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
        }
        return CONF_URL_TEST;
  
    /*if ($path) {
        return CONF_URL_BASE . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);
    }

    return CONF_URL_BASE;*/
}



function is_passwd(string $password): bool
{
    if (password_get_info($password)['algo'] || (mb_strlen($password) >= CONF_PASSWD_MIN_LEN && mb_strlen($password) <= CONF_PASSWD_MAX_LEN)) {
        return true;
    }

    return false;
}

function passwd(string $password): string
{
    if (!empty(password_get_info($password)['algo'])) {
        return $password;
    }

    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}

/**
 * @param string $password
 * @param string $hash
 * @return bool
 */
function passwd_verify(string $password, string $hash): bool
{
    return password_verify($password, $hash);
}

/**
 * @param string $hash
 * @return bool
 */
function passwd_rehash(string $hash): bool
{
    return password_needs_rehash($hash, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);
}


function  novaVenda(){


        $_SESSION['caixa']['status']="aberto";
        $_SESSION['caixa']['inicio']="S";

        return url("/homecaixa");
}                                                                                                                                         
function redirect(string $url): void
{
    header("HTTP/1.1 302 Redirect");
    if (filter_var($url, FILTER_VALIDATE_URL)) {
        header("Location: {$url}");
        exit;
    }

    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {
        $location = url($url);
        header("Location: {$location}");
        exit;
    }
}

/*Tratamento de imagens*/
function imgBlob($param){

	echo $img='<img src="data:image/png;base64,'.base64_encode($param).'" class="foto" width="40px" height="40px" style="opacity: .8">';

	
}

function imgBlobGeral($param,string $classe=""){

    echo $img='<img src="data:image/png;base64,'.base64_encode($param).'" class="'.$classe.'" id="foto-dinamico" width="20px" height="20">';

    
}







function imgBlobGeralCardio($param,string $classe=""){

    echo $img='<img src="data:image/png;base64,'.base64_encode($param).'" class="'.$classe.'" id="foto-dinamico" width="32%" height="100" >';

    
}

function converteArray($dados): ?array
{
   $l=[];
        foreach ($dados as $datas) {
          
           $l=$datas;
        }

       return $l;
}

/*flash para mensagens*/

function flash(): ?string
{
    $session = new \Source\Core\Session();
    if ($flash = $session->flash()) {
        echo $flash;
    }
    return null;
}


/* formatos de datas*/

function date_fmt_app(string $date = "now"): string
{
    return (new DateTime($date))->format("Y-m-d");
}

function date_fmt_app_day(string $date = "now"): string
{
    return (new DateTime($date))->format("Y-m-d H:i:s");
}
function dia(string $date = "now"): string
{
    return (new DateTime($date))->format("H:i:s");
}

function date_fmt_Normal(string $date = "now"): string
{
    return (new DateTime($date))->format("d-m-Y");
}
/*FINANCAS*/

function ivaTaxa(int $iva, $dinheiro)
{
   $i= $iva / 100;

   $taxa=$dinheiro * $i;

   return $taxa;

}

function lucro(int $qtd, $valCompra,$valVenda)
{
   $i= $qtd * $valVenda;

   $taxa=$qtd * $valCompra;

   return $taxa;

}

/*MENSANGENS*/

function smsSucess(string $parama): ?string
{
   $render='
    <div class="msg-success">
                 <span class="fas fa-check-circle"></span>
                 <h2>'.$parama.'</h2>
              </div>
   ';

  return $render;
}



function smsInfo(string $parama): ?string
{
   $render='
    <div class="msg-info">
                 <span class="fas fa-info-circle"></span>
                 <h2>'.$parama.'</h2>
              </div>
   ';

  return $render;
}

function smsError(string $parama): ?string
{

   $render='
    <div class="msg-error">
                 <span class="fas fa-plus-circle"></span>
                 <h2>'.$parama.'</h2>
              </div>
   ';

  return $render;
}

function factura(){
    $fat=(new Source\Model\Factura());

    return  $fat;
}

function gerarNumFactura($tipo){

   
    $cas=factura();
   $cas->numeroFatura($tipo);
    $nu=$cas->numFatura+1;

    $fatura=$tipo." ".$nu."SV".$cas->anoFatura."/".$nu;
    return $fatura;
}

function tipoNumeroFactura($tipo): int
{

   
    $cas=factura();
   $cas->numeroFatura($tipo);
    $nu=$cas->numFatura+1;


    return  $nu;
}

function str_price($preco){
 
 return number_format($preco,"2",",",".");
}

/**
 * função que diz quantos itens tem em uma mesa
 * @param int $id_mesa
 * @return string
 * **/

function mesaDetalhes(int $id_mesa): void
{
    $fat=(new Source\Models\Mesa\Mesa());
    $qtd=0;
    $total="0";
    
    $quant=$fat->select("id_mesa=:id AND situacao='DIVIDA'","id={$id_mesa}")->fitch(true);
    if ($quant) {
       
       foreach ($quant as $t) {
           $qtd++;
           $total+=$t->total;
       }
    }
    echo "
        <p> Qtd de Produtos: ". $qtd."</p>
        <p style='    font-size: 14px;
    font-style: oblique;
    font-family: sans-serif;
    font-weight: 900;'> valor Total: ". str_price($total)." KZ</p>
    ";
}

function cliente($id)
{
     $fat=(new Source\Models\Client());
     $st=$fat->select("id='{$id}'")->fitch();

     echo $st->nome;
}

function listPermissao($id,$acesso)
{
     $fat=(new Source\Models\UserAcess());
     $st=$fat->select("id_user='{$id}' AND id_modulo_acess='{$acesso}'")->fitch();
     
     if (!$st) {
       return false;
     }
      return true;
}

function operador($id)
{
     $fat=(new Source\Models\User());
     $st=$fat->select("id_pessoa='{$id}'")->fitch();
     
     if (!$st) {
       return "Desconhecido";
     }
      return $st->nome;
}

function clientes($id)
{
     $fat=(new Source\Models\Client());
     $st=$fat->select("id='{$id}'")->fitch();
     
     if (!$st) {
       return "Desconhecido";
     }
      return $st->nome;
}



/**
 * Verifica se um email é válido.
 *
 * @param string $email O email a ser verificado.
 * @return bool Retorna true se o email for válido, false caso contrário.
 */
function isValidEmail(string $email): bool
{
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}


function encryptString($data, $key="Delcoom")
{
    // Gerar um vetor de inicialização (IV)
    $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

    // Criptografar os dados
    $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $key, 0, $iv);

    // Codificar em Base64 e concatenar com o IV
    return base64_encode($encryptedData . '::' . $iv);
}


function decryptString($encryptedData, $key="Delcoom")
{
    // Decodificar Base64 e separar os dados criptografados do IV
    list($encryptedData, $iv) = explode('::', base64_decode($encryptedData), 2);

    // Descriptografar os dados
    return openssl_decrypt($encryptedData, 'aes-256-cbc', $key, 0, $iv);
}





