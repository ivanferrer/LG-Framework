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
	protected $QueryBuilder;
	
	public function __construct(){
		try{@$this->con = new \PDO(null, null, null, null);}catch(\PDOException $e){} // hack para IDE interpretar $this->con como objeto PDO
		$this->QueryBuilder = new QueryBuilder();
	}
	
	private function prepareStatement($obj,$operacao){
		if(!$this->preparedStatus){
			$this->QueryBuilder->setCamposValores($this->campos, $this->valores);
			$this->statement = $this->con->prepare($this->QueryBuilder->getQuery($obj, $operacao));
			$this->preparedStatus = true;
		}
	}
	
	private function setParams($obj){
		$gets = $this->getMetodos($obj,"get");
		$attrs = $this->getAtributos($obj);
		foreach($attrs as $key => $campo){
			$value = $obj->{$gets[$key]}();
		    if(!is_null($value)){
    			$this->campos[$key] = $campo;
    			$this->valores[$key] = $value;
		    }
		}
	}
	
	private function bindParams(){
		foreach($this->campos as $key => &$value){
			$this->statement->bindParam($value, $this->valores[$key]);
		}
	}
	
	public function getId(Modelo $obj,$id){
		$this->con = parent::getConnection();
	    $this->QueryBuilder->addWhereEquals($obj->getPrimaryKey(), $id);
	    $result = $this->con->query($this->QueryBuilder->getQuery($obj, "SELECT"));
	    $result->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, get_class($obj));
	    return $result->fetch();
	}
	
	/**
	 * @param Modelo $obj
	 * @return SplObjectStorage (Lista/Array de Objetos)
	 * @throws \PDOException
	 */
	public function select(Modelo $obj){
		$this->con = parent::getConnection();
		$list = new \SplObjectStorage();
		$result = $this->con->query($this->QueryBuilder->getQuery($obj, "SELECT"));
		$classe = strtolower(get_class($obj));
		while($row = $result->fetch(\PDO::FETCH_ASSOC)){
			$obj = new $classe();
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
		$this->setParams($obj);
		$this->prepareStatement($obj,"INSERT");
		$this->bindParams();
		$erro = $this->statement->execute();
		$this->statement->errorInfo();
		$obj->setIncrementId($this->con->lastInsertId());
	}
	
	public function update(Modelo $obj,$camposUpdate = null){
		$this->con = parent::getConnection();
	    
	}
	
	/**
	 * 
	 * @param Modelo $obj
	 * @param String $tipo Deve ser "get" ou "set"
	 * @return array de getters
	 */
	private function getMetodos(Modelo $obj, $tipo){
		if($tipo != "get" && $tipo != "set")
			throw new e\PHPException("0", 'Vari�vel $tipo deve ser "get" ou "set"', __FILE__, __LINE__);
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
	
	public function commit(){
	    $this->con->commit();
	}
	
	public function rollBack(){
	    $this->con->rollBack();
	}
}