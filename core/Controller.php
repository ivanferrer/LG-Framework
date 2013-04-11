<?php
namespace core;
use lib as l;
use exceptions as e;
abstract class Controller {
	private $metodosAutenticados;
	private $metodosNaoAutenticados;
	protected $generalContent;
	protected $template;
	protected $alertaMensagem;
	protected $alertaClasse;
	
	abstract public function index();
	
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
    	    $call = '\model\\'.Globals::getClasse();
    	    $modelo = new $call();
    	    l\Functions::setObjectFromArray($modelo, $_POST);
    	    
    	    $call = '\dao\\'.Globals::getClasse();
    	    $dao = new $call();
    	    $dao->insert($modelo);
	        $this->setAlerta("Cadastro Realizado com sucesso!",'success');
	        $dao->commit();
	    }catch(e\ModeloException $e){
	        $this->setAlerta($e->getMessage(),'alert');
	        $dao->rollBack();
	    }catch(\PDOException $e){
	        $this->setAlerta($e->getMessage(),'alert');
	        $dao->rollBack();
	    }catch(\Exception $e){
	        $this->setAlerta($e->getMessage(),'alert');
	        $dao->rollBack();
	    }
    	$anterior = Globals::getAnterior();
	    header("Location: ".HTTP_PATH.$anterior['urlChamada'],true,307);
	}
	
	public function setAlerta($mensagem,$classe){
		$this->alertaMensagem = $mensagem;
		$this->alertaClasse = $classe;
		$_SESSION['LGF_alerta']['mensagem'] = $mensagem;
		$_SESSION['LGF_alerta']['classe'] = $classe;
	}

}
