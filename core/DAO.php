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
	
	private $con;
	private $query;
	private $preparedStatus;
	private $statement;
	private $campos;
	private $valores;
	private $operacaoMultipla;
	protected $QueryBuilder;
	
	public function __construct(){
		try{@$this->con = new \PDO(null, null, null, null);}catch(\PDOException $e){} // hack para IDE interpretar $this->con como objeto PDO
		$this->QueryBuilder = new QueryBuilder();
    	$this->campos = array();
    	$this->valores = array();
    	$this->operacaoMultipla = false;
	    $this->preparedStatus = false;
	}
	
	private function prepareStatement($obj,$operacao){
		if(!$this->preparedStatus){
		    if(!$this->QueryBuilder->issetCampos()){
		        $this->QueryBuilder->setCamposValores($this->campos, $this->valores);
		    }
		    $sql = $this->QueryBuilder->getQuery($obj, $operacao);
			$this->statement = $this->con->prepare($sql);
		}
	}
	
	public function startOperacaoMultipla(){
	    $this->operacaoMultipla = true;
	}
	
	private function stopOperacaoMultipla(){
	    $this->operacaoMultipla = false;
	    $this->preparedStatus = false;
	}
	
	private function setParams($obj,$ignoreKey = false){
		$gets = $this->getMetodos($obj,"get");
		$attrs = $this->getAtributos($obj);
		foreach($attrs as $key => $campo){
			$value = $obj->{$gets[$key]}();
		    if(!is_null($value)){
		        if($ignoreKey){
		            if($campo != $obj->getPrimaryKey()){
            			$this->campos[$key] = $campo;
            			$this->valores[$key] = $value;
		            }
		        }else{
        			$this->campos[$key] = $campo;
        			$this->valores[$key] = $value;
		        }
		    }
		}
	}
	
	private function bindParams(){
		foreach($this->campos as $key => &$value){
			$this->statement->bindParam($value, $this->valores[$key]);
		}
	}
	
	public function getId(Modelo $obj,$id){
		$this->startTransaction();
		
    	    $this->QueryBuilder->addWhereEquals($obj->getPrimaryKey(), $id);
    	    $result = $this->con->query($this->QueryBuilder->getQuery($obj, "SELECT"));
    	    $result->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, get_class($obj));
    	    
		$this->encerrarQuery();
	    return $result->fetch();
	}
	
	/**
	 * @param Modelo $obj
	 * @return SplObjectStorage (Lista/Array de Objetos)
	 * @throws \PDOException
	 */
	public function select(Modelo $obj){
		$this->startTransaction();
		
    		$list = new \SplObjectStorage();
    		$result = $this->con->query($this->QueryBuilder->getQuery($obj, "SELECT"));
    		$classe = strtolower(get_class($obj));
    		while($row = $result->fetch(\PDO::FETCH_ASSOC)){
    			$obj = new $classe();
    			$obj = l\Functions::setObjectFromArray($obj, $row);
    			$list->attach($obj);
    		}
		
		$this->encerrarQuery();
		return $list;
	}
	
	/**
	 * 
	 * @param Modelo $obj 
	 * @throws PDOException
	 */
	public function insert(Modelo $obj){
		$this->startTransaction();
		
    		$this->setParams($obj);
    		$this->prepareStatement($obj,"INSERT");
    		$this->bindParams();
    		$erro = $this->statement->execute();
    		$this->statement->errorInfo();
    		$obj->setIncrementId($this->con->lastInsertId());
		
		$this->encerrarQuery();
	}
	
	public function update(Modelo $obj){
		$this->startTransaction();
		
    		$this->setParams($obj,true);
    		$call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ", $obj->getPrimaryKey())));
    		$id = $obj->$call();
    	    $this->QueryBuilder->addWhereEquals($obj->getPrimaryKey(), $id);
    		$this->prepareStatement($obj,"UPDATE");
    		$this->bindParams();
    		$erro = $this->statement->execute();
    		$this->statement->errorInfo();	    
		
		$this->encerrarQuery();
	}
	

	protected function query($sql){
		$this->startTransaction();
		$result = $this->con->query($sql);
		while($row = $result->fetch(\PDO::FETCH_ASSOC)){
			$return[] = $row;
		}
		return $return;
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
	
	
	private function encerrarQuery(){
    	if($this->operacaoMultipla){
    	    $this->preparedStatus = true;
    	}else{
    	    $this->__construct();
    	}
	}
	public function startTransaction(){
        $this->con = parent::getConnection();
	    if(!ConnectionFactory::trasactionAtiva()){
	        ConnectionFactory::beginTransaction();
	    }
	}
	
	public function commit(){
	    $this->__construct();
	    if($this->con instanceof \PDO){
    	    if(ConnectionFactory::trasactionAtiva()){
        	    $this->con->commit();
                ConnectionFactory::endTransaction();
    	    }
	    }
	}
	
	public function rollBack(){
	    $this->__construct();
	    if($this->con instanceof \PDO){
    	    if(ConnectionFactory::trasactionAtiva()){
        	    $this->con->rollBack();
                ConnectionFactory::endTransaction();
    	    }
	    }
	}
}