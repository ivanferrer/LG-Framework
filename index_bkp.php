<?php 
/**
 * @author Luiz Guilherme (luizguilherme00@hotmail.com)
 * @package LGFramework
 * @version 1.0
 */
$_app_dir = __DIR__;
require_once("../../LGFramework/v1/system/load_framework.php");
use \exceptions as e;
use \core as c;
// instancia o controller e chama o metodo, conforme requisitado na URI
// caso não encontre, lança uma exception
try{
	$roteador = new c\Roteador($_GET['sysAction']);
	$roteador->instanciarObj();
	$roteador->registrarSession();
	$roteador->verificarPermissao();
	$roteador->chamarMetodo();
	$roteador->exibir();
}catch(e\PermissionException $e){
	$_SESSION['LGF_alerta']['mensagem'] = $e->getMessage();
	$_SESSION['LGF_alerta']['classe'] = "alert";
	header("Location: ".HTTP_PATH.VIEW_LOGIN."/".METODO_LOGIN,true,307);
}catch(e\PageException $e){
	if($e->getCode() == 404){
		$smarty = new c\Smarty();
		$smarty->display('404.tpl');
	}
}catch(e\SetupException $e){
	$setup = new c\Setup();
	$setup->montarFormulario();
	$setup->tratarAcao();
	$setup->exibir();
}catch(\Exception $e){
	e\ExceptionHandler::tratarErro($e);
}
?>