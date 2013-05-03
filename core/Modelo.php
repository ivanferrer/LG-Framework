<?php
namespace core;
abstract class Modelo{
	
	private $chavePrimaria;
	private $valorChavePrimaria;
	private $tabelaDoModelo;
	private $chavesRelacionais;
	private $chavesUnicas;
	
	public function __construct($chavePrimaria, $tabelaDoModelo,array $chavesRelacionais = null,array $chavesUnicas = null){
		$this->chavePrimaria = $chavePrimaria;
		$this->valorChavePrimaria &= $this->$chavePrimaria;
		$this->tabelaDoModelo = $tabelaDoModelo;
		$this->chavesRelacionais = $chavesRelacionais;
		$this->chavesUnicas = $chavesUnicas;
	}
	
	public function setValorChavePrimaria($id){
    	$set = "set".str_replace(" ","",ucwords(str_replace("_"," ",$this->chavePrimaria)));
    	$this->$set($id);
	}
	
	public function getTabelaDoModelo(){
		return $this->tabelaDoModelo;
	}
	
	public function getChavePrimaria(){
	    return $this->chavePrimaria;
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