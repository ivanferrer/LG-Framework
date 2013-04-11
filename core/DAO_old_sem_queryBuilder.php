<?php
/**
 * @author Luiz Guilherme (luizguilherme00@hotmail.com)
 * @package LGFramework
 * @version 1
 */
namespace core;
use lib as l;
use exceptions as e;
abstract class DAO extends ConnectionFactory{
	
	public $con;
	private $query;
	private $preparedStatus;
	private $statement;
	private $campos;
	private $valores;
	
	public function __construct(){
		try{@$this->con = new \PDO(null, null, null, null);}catch(\PDOException $e){} // hack para IDE interpretar $this->con como objeto PDO
	}
	
	private function prepareStatement($obj){
		if(!$this->preparedStatus){
			$this->setParams($obj);
			$this->statement = $this->con->prepare("insert into ".$obj->getTabela()." (".implode(",",$this->campos).") values(:".implode(",:",$this->campos).")");
			$this->preparedStatus = true;
			$this->bindParams();
		}
	}
	
	private function setParams($obj){
		$gets = $this->getMetodos($obj,"get");
		$attrs = $this->getAtributos($obj);
		foreach($attrs as $key => $campo){
			$this->campos[$key] = $campo;
			$value = $obj->{$gets[$key]}();
			$this->valores[$key] = (is_null($value)) ? "NULL" : $value;
		}
	}
	
	private function bindParams(){
		foreach($this->campos as $key => &$value){
			$this->statement->bindParam($value, $this->valores[$key]);
		}
	}
	
	/**
	 * @param Modelo $obj
	 * @return SplObjectStorage (Lista/Array de Objetos)
	 * @throws \PDOException
	 */
	public function select(Modelo $obj,$where = ''){
		$this->con = parent::getConnection();
		$list = new \SplObjectStorage();
		$nome = strtolower(get_class($obj));
		$class = 'call';
		$$class = $nome;
		$result = $this->con->query("select * from ".$obj->getTabela()." ".$where);
		while($row = $result->fetch(\PDO::FETCH_ASSOC)){
			$obj = new $call();
			$obj = l\Functions::setObjectFromArray($obj, $row);
			$list->attach($obj);
		}
		return $list;
	}
	
	/**
	 * 
	 * @param Modelo $obj 
	 * @throws PDOException
	 */
	public function insert(Modelo $obj){
		$this->con = parent::getConnection();
		$this->prepareStatement($obj);
		$this->setParams($obj);
		$erro = $this->statement->execute();
		$this->statement->errorInfo();
		$obj->setIncrementId($this->con->lastInsertId());
	}
	
	/**
	 * 
	 * @param Modelo $obj
	 * @param String $tipo Deve ser "get" ou "set"
	 * @return array de getters
	 */
	private function getMetodos(Modelo $obj, $tipo){
		if($tipo != "get" && $tipo != "set")
			throw new e\PHPException("0", 'Variável $tipo deve ser "get" ou "set"', __FILE__, __LINE__);
		$_ref = new \ReflectionClass(get_class($obj));
		$_ret = $_ref->getMethods(\ReflectionMethod::IS_PUBLIC);
		foreach($_ret as $key => $value){
			if(strpos($value->name, $tipo) === 0){
				$_return[] = $value->name;
			}
		}
		return $_return;
	}
	private function getAtributos(Modelo $obj){
		$_ref = new \ReflectionClass(get_class($obj));
		$_ret = $_ref->getProperties();
		foreach($_ret as $_attributo){
			$_return[] = $this->converterNomeBanco($_attributo->name);
		}
		return $_return;
	}

	private function converterNomeBanco($campo){
		preg_match_all( '/[A-Z]/', $campo, $matches);
		foreach($matches[0] as $value){
			$campo = str_replace($value,"_".strtolower($value), $campo);
		}
		return $campo;
	}
	
	public function update($obj){
		$this->con = parent::getConnection();
		
	}
}