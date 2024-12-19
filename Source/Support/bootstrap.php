<?php



function date_fmt_app(string $date = "now"): string

{

    return (new DateTime($date))->format(CONF_DATE_APP_DATE);

}



function passwd(string $password): string

{

    if (!empty(password_get_info($password)['algo'])) {

        return $password;

    }



    return password_hash($password, CONF_PASSWD_ALGO, CONF_PASSWD_OPTION);

}



function passwd_verify(string $password, string $hash): bool

{

    return password_verify($password, $hash);

}



function url(string $path = null): string

{

   

   

            return CONF_URL_TEST . "/" . ($path[0] == "/" ? mb_substr($path, 1) : $path);

    

}





function redirect(string $url): void

{

    header("HTTP/1.1 302 Redirect");

    if (filter_var($url, FILTER_VALIDATE_URL)) {

        header("Location: {$url}");

        exit;

    }



    if (filter_input(INPUT_GET, "route", FILTER_DEFAULT) != $url) {

        $location = $url;

        header("Location: {$location}");

        exit;

    }

}



function validateForm($part): bool

{

    

    if (empty($part)) {

        

        echo "O Campo ".$part." é obrigatório";

        return true;

    }



    return false;

}



/*MENSANGENS*/



function smsSucess(string $parama)?string

{

   $render='

    <div class="msg">

                 <span class="fas fa-check-circle"></span>

                 <h2>{$parama}</h2>

              </div>

   ';



  return $render;

}







function smsInfo(string $parama) ?string

{

   $render='

    <div class="msg-info">

                 <span class="fas fa-info-circle"></span>

                 <h2>{$parama}</h2>

              </div>

   ';



  return $render;

}



function smsError(string $parama) ?string

{



   $render='

    <div class="msg-error">

                 <span class="fas fa-plus-circle"></span>

                 <h2>{$parama}</h2>

              </div>

   ';



  return $render;

}





