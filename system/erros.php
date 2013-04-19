<?php
use \exceptions as e;
use \core as c;

// Este código fará com que todo erro seja tratado como exception
set_error_handler("handleError", E_ALL );
error_reporting(0);

// Este fará com que FATAL ERRORS gerem log;
register_shutdown_function('handleShutdown');

function handleError($a,$b,$c,$d){
	e\PHPException::throwError($a, $b, $c, $d);
}

function handleShutdown() {
	$error = error_get_last();
	if(strstr($error['file'],"autoload.php") === false){
		if($error !== NULL){
			$e = new \Exception($error['message'].$error['line'].$error['file']);
			$mensagem =  e\ExceptionHandler::tratarErro($e);
        	$smarty = new c\Smarty();
        	$smarty->assign("mensagem",$mensagem);
        	$smarty->display('500.tpl');
		}else{
			//yourPrintOrMailFunction("SHUTDOWN");
		}
	}
}

?>