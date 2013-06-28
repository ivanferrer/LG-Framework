<?php
// DEFINE PATHS
define("DS",\DIRECTORY_SEPARATOR);
define("APP_DIR",$_app_dir.DS);

$framework = str_replace('system', '', __DIR__);
define("LGF_PATH",$framework);
define('DOMINIO_URL',$_SERVER['HTTP_HOST']);
define("HTTP_PATH",substr($_SERVER['SCRIPT_NAME'],0,strrpos($_SERVER['SCRIPT_NAME'], "/")+1));
$protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
define("HTTP_FULL_PATH",$protocol.DOMINIO_URL.HTTP_PATH);
define("HTTP_FULL_PATH_LOCAL","http://127.0.0.1".HTTP_PATH);

unset($protocol);
unset($_app_dir);
unset($pastas);
unset($framework);
?>