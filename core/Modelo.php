<?php
namespace core;
abstract class Modelo{
	
	private $primaryKey;
	private $tabela;
	
	public function __construct($primaryKey, $tabela){
		$this->primaryKey = $primaryKey;
		$this->tabela = $tabela;
	}
	
	public function setIncrementId($id){
    	$set = "set".str_replace(" ","",ucwords(str_replace("_"," ",$this->primaryKey)));
    	$this->$set($id);
	}
	
	public function getTabela(){
		return $this->tabela;
	}
	
	public function getPrimaryKey(){
	    return $this->primaryKey;
	}

	
}