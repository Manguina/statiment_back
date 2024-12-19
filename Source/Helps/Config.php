<?php
define("CONF_DB_HOST", "localhost");
define("CONF_DB_USER", "root");
define("CONF_DB_PASS", "");
define("CONF_DB_NAME", "statement_post");

define("CONF_URL_TEST", "http://localhost/Blog");
define("CONF_URL_ADMIN", "/admin");
define("CONF_URL_TEMPLATE", CONF_URL_TEST."/Public/");
define("URL_CSS_JS", CONF_URL_TEST."/Public/assets");
define("RELATORIO", CONF_URL_TEST."/Public/Reports/");

/**
 * LOGO PADRÃO
 * **/
define("LOGO", URL_CSS_JS."/img/logo2.png");


 /* DATES
 */
define("CONF_DATE_BR", "d/m/Y H:i:s");
define("CONF_DATE_APP_DATE", "Y-m-d");
define("CONF_DATE_APP", "Y-m-d H:i:s");

/*BACKUP*/
define("PASTA_BACKUP","C:\SOLVE\Backup\\");
define("MYSQL_DUMP","C:\wamp\bin\mysql\mysql5.7.31\bin\mysqldump.exe  -u root verao_bd");
/*FIM BACKUP*/

/**
 * PASSWORD
 */
define("CONF_PASSWD_MIN_LEN", 4);
define("CONF_PASSWD_MAX_LEN", 40);
define("CONF_PASSWD_ALGO", PASSWORD_DEFAULT);
define("CONF_PASSWD_OPTION", ["cost" => 10]);

/**
 * MESSAGE
 */
define("CONF_MESSAGE_CLASS", "alert");
define("CONF_MESSAGE_INFO", "alert-info alert-dismissible");
define("CONF_MESSAGE_SUCCESS", "alert-success alert-dismissible");
define("CONF_MESSAGE_WARNING", "alert-warning alert-dismissible");
define("CONF_MESSAGE_ERROR", "alert-danger alert-dismissible");
