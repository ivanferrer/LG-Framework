<?php

$file = APP_DIR."config".DS."apps.conf";
$app = "";
if(file_exists($file)){
    $actions = explode("/", $_GET['sysAction']);
    $linhas = file($file);
    foreach($linhas as $linha){
        $info = explode("=", $linha);
        $appName = trim($info[0]);
        if("\\".strtolower($actions[0]) == strtolower($appName)){
            $app = strtolower($actions[0]);
        }
    }
}

define("LGF_SUBAPP",$app);