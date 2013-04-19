<?php
namespace core;
use lib as l;
use exceptions as e;
abstract class Controller {
	private $metodosAutenticados;
	private $metodosNaoAutenticados;
	protected $generalContent;
	protected $template;
		
	public function __construct() {
		$this->metodosAutenticados = array();
		$this->metodosNaoAutenticados = array();
	}

	protected function setMetodosAutenticados($valor) {
		$this->metodosAutenticados = $valor;
	}
	protected function setMetodosNaoAutenticados($valor) {
		$this->metodosNaoAutenticados = $valor;
	}

	public function getMetodosAutenticados() {
		return $this->metodosAutenticados;
	}
	public function getMetodosNaoAutenticados() {
		return $this->metodosNaoAutenticados;
	}
	
	public function inserir(){
	    try{
    	    $call = '\dao\\'.Globals::getClasse();
    	    $dao = new $call();
    	    
    	    Globals::setAlertTipo("alert");
    	    $call = '\model\\'.Globals::getClasse();
    	    $modelo = new $call();
    	    l\Functions::setObjectFromArray($modelo, $_POST);
    	    
    	    $dao->insert($modelo);
    	    Globals::setAlertTipo("success");
    	    Globals::setAlertMensagem("Cadastro Realizado com sucesso!");
	        $dao->commit();
	    }catch(e\ModeloException $e){
	        Globals::setAlertMensagem($e->getMessage());
	        $dao->rollBack();
	        echo"oi";
	    }catch(\PDOException $e){
	        Globals::setAlertMensagem($e->getMessage());
	        $dao->rollBack();
	    }catch(\Exception $e){
	        Globals::setAlertMensagem($e->getMessage());
	        $dao->rollBack();
	    }
    	$anterior = Globals::getAnterior();
	    header("Location: ".HTTP_PATH.$anterior['urlChamada'],true,307);
	}
	
	/*
	public function setAlerta($mensagem,$classe){
		$this->alertaMensagem = $mensagem;
		$this->alertaClasse = $classe;
		$_SESSION['LGF_alerta']['mensagem'] = $mensagem;
		$_SESSION['LGF_alerta']['classe'] = $classe;
	}*/

}
