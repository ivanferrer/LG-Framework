<?php
namespace core;
use exceptions as e;
abstract class Modelo{
	
	private $chavePrimaria;
	private $valorChavePrimaria;
	private $tabelaDoModelo;
	private $chavesRelacionais;
	private $chavesUnicas;
	private $autoIncrement;
	
	public function __construct($chavePrimaria, $tabelaDoModelo,$autoIncrement,array $chavesRelacionais = null,array $chavesUnicas = null){
		$this->chavePrimaria = $chavePrimaria;
		if(is_array($chavePrimaria)){
		    foreach($chavePrimaria as $pk){
		        $this->valorChavePrimaria[$pk] =& $this->$pk;
		    }
		}else{
		    $this->valorChavePrimaria =& $this->$chavePrimaria;
		}
		$this->tabelaDoModelo = $tabelaDoModelo;
		$this->tabelaDoModelo = $tabelaDoModelo;
		$this->autoIncrement = $autoIncrement;
		$this->chavesUnicas = $chavesUnicas;
		$this->chavesRelacionais = $chavesRelacionais;
	}
	
	public function setValorChavePrimaria($valor){
	    if(is_array($this->chavePrimaria)){
	        if(is_array($valor) && count($valor) == count($this->chavePrimaria)){
	            foreach($valor as $key => $val){
	                $this->valorChavePrimaria[$key] = $val;
	            }
	        }else{
	          throw new e\ModeloException("Valor fornecido para chave primária não bate com quantidade de chaves da tabela.");
	        }
	    }else{
        	//$set = "set".str_replace(" ","",ucwords(str_replace("_"," ",$this->chavePrimaria)));
        	$this->valorChavePrimaria = $valor;
	    }
	}
	
	public function getTabelaDoModelo(){
		return $this->tabelaDoModelo;
	}
	
	public function getChavePrimaria(){
	    return $this->chavePrimaria;
	}
	
	public function setAutoIncrementId($id){
    	$set = "set".str_replace(" ","",ucwords(str_replace("_"," ",$this->autoIncrement)));
    	$this->$set($id);
	}

	public function getAutoIncrementId(){
	    if(is_array($this->chavePrimaria)){
	        return $this->valorChavePrimaria[$this->autoIncrement];
	    }else{
	        return $this->valorChavePrimaria;
	    }
	}
	
	public function getValorChavePrimaria(){
	    return $this->valorChavePrimaria;
	}
	
	public function getChavesRelacionais(){
	    return $this->chavesRelacionais;
	}
	
	public function getChavesUnicas(){
	    return $this->chavesUnicas;
	}
}