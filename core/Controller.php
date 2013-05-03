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
	
	public function listar(){
	        return $this->dao->select($this->model);
	}
	
	public function inserir($json = false){
	    try{
    	    Globals::setAlertTipo("alert");
    	    $arr = $_POST;
	        if($json){
                foreach($_POST as $key => $value){
                    $arr[$key] = utf8_decode($value);
                }
	        }
	        l\Functions::setObjectFromArray($this->model, $arr);
    	    
    	    $this->dao->insert($this->model);

    	    $call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ", $this->model->getChavePrimaria())));
    	    $json_resp['id'] = $this->model->$call();
    	    
    	    Globals::setAlertTipo("success");
            $nome = explode('\\', get_class($this));
    	    Globals::setAlertMensagem($nome[1]." inserido com sucesso!");
	        $this->dao->commit();
	    }catch(\Exception $e){
    	    Globals::setAlertTipo("error");
	        Globals::setAlertMensagem($e->getMessage());
	        $this->dao->rollBack();
	        e\ExceptionHandler::tratarErro($e);
	    }
	    if($json){
    	    $json_resp['status'] = Globals::getAlertTipo();
    	    $json_resp['mensagem'] = Globals::getAlertMensagem();
	        //echo json_encode($json_resp);
	        echo l\Functions::toJson($json_resp);
	    }else{
        	$anterior = Globals::getAnterior();
    	    header("Location: ".HTTP_PATH.$anterior['urlChamada'],true,307);
	    }
	}
	
	public function alterar($json = false){
	    try{
    	    Globals::setAlertTipo("alert");
    	    $arr = $_POST;
	        if($json){
                foreach($_POST as $key => $value){
                    $arr[$key] = utf8_decode($value);
                }
	        }
	        l\Functions::setObjectFromArray($this->model, $arr);
    	    $this->dao->update($this->model);
    	    
    	    Globals::setAlertTipo("success");
            $nome = explode('\\', get_class($this));
    	    Globals::setAlertMensagem($nome[1]." alterado com sucesso!");
    	    $this->dao->commit();
	    }catch(\Exception $e){
    	    Globals::setAlertTipo("error");
	        Globals::setAlertMensagem($e->getMessage());
	        $this->dao->rollBack();
	        e\ExceptionHandler::tratarErro($e);
	    }
	    
	    if($json){
    	    $json_resp['status'] = Globals::getAlertTipo();
    	    $json_resp['mensagem'] = utf8_encode(Globals::getAlertMensagem());
	        echo l\Functions::toJson($json_resp);
	    }else{
        	$anterior = Globals::getAnterior();
    	    header("Location: ".HTTP_PATH.$anterior['urlChamada'],true,307);
	    }
	}
	
	public function excluir($json = false){
	    try{
    	    Globals::setAlertTipo("alert");
    	    $arr = $_POST;
	        if($json){
                foreach($_POST as $key => $value){
                    $arr[$key] = utf8_decode($value);
                }
	        }
	        l\Functions::setObjectFromArray($this->model, $arr);
	        
    	    $this->dao->delete($this->model);
    	    
    	    Globals::setAlertTipo("success");
            $nome = explode('\\', get_class($this));
    	    Globals::setAlertMensagem($nome[1]." excluído com sucesso!");
	        $this->dao->commit();
	    }catch(\Exception $e){
    	    Globals::setAlertTipo("error");
	        Globals::setAlertMensagem($e->getMessage());
	        $this->dao->rollBack();
	        e\ExceptionHandler::tratarErro($e);
	    }
	    
	    if($json){
    	    $json_resp['status'] = Globals::getAlertTipo();
    	    $json_resp['mensagem'] = Globals::getAlertMensagem();
	        echo l\Functions::toJson($json_resp);
	    }else{
        	$anterior = Globals::getAnterior();
    	    header("Location: ".HTTP_PATH.$anterior['urlChamada'],true,307);
	    }
	}
	
	/*
	public function setAlerta($mensagem,$classe){
		$this->alertaMensagem = $mensagem;
		$this->alertaClasse = $classe;
		$_SESSION['LGF_alerta']['mensagem'] = $mensagem;
		$_SESSION['LGF_alerta']['classe'] = $classe;
	}*/

}
