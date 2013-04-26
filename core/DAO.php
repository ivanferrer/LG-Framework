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
	private $tipoOperacao;
	protected $QueryBuilder;
	
	public function __construct(){
		try{@$this->con = new \PDO(null, null, null, null);}catch(\PDOException $e){} // hack para IDE interpretar $this->con como objeto PDO
		$this->QueryBuilder = new QueryBuilder();
    	$this->campos = array();
    	$this->valores = array();
    	$this->operacaoMultipla = false;
	    $this->preparedStatus = false;
	}
	
	private function prepareStatement($obj){
		if(!$this->preparedStatus){
		    if(!$this->QueryBuilder->issetCampos()){
		        $this->QueryBuilder->setCamposValores($this->campos, $this->valores);
		    }
		    $camposWhere = $this->QueryBuilder->getParametroWhere();
		    $sql = $this->QueryBuilder->getQuery($obj, $this->tipoOperacao);
			$this->statement = $this->con->prepare($sql);
			return $camposWhere;
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
		    if(!is_null($value) && $this->tipoOperacao != 'SELECT'){
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
	
	private function bindParams($extra = null){
		if(!is_null($extra) && count($extra) > 0){
		    foreach($extra as $key => $value){
		        $this->statement->bindValue($key,$value);
		    }
		}elseif($this->tipoOperacao != 'SELECT'){
    		foreach($this->campos as $key => &$value){
    			$this->statement->bindValue($value, $this->valores[$key]);
    		}
		}
	}
	
	public function getId(Modelo $obj,$id){
	    try{
    		$this->QueryBuilder->addWhereEquals($obj->getPrimaryKey(), $id);
    		$list = $this->select($obj);
    		$list->rewind();
    		return $list->current();
	    }catch(\PDOException $e){
	        throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
	    }
	}
	
	/**
	 * @param Modelo $obj
	 * @return SplObjectStorage (Lista/Array de Objetos)
	 * @throws \PDOException
	 */
	public function select(Modelo $obj){
	    try{
    		$this->startTransaction();

    		    $this->tipoOperacao = "SELECT";
        		$list = new \SplObjectStorage();
        		$this->setParams($obj);
        		$extraParams = $this->prepareStatement($obj);
        		$this->bindParams($extraParams);
        		
        		$erro = $this->statement->execute();
        		
        		$this->statement->errorInfo();
        		$classe = strtolower(get_class($obj));
        	    $this->statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, get_class($obj));
        		while($obj = $this->statement->fetch()){
        			$list->attach($obj);
        		}
    		$this->encerrarQuery();
    		return $list;
	    }catch(\PDOException $e){
	        throw new e\DaoException($e->getMessage());
	    }
	}
	
	/**
	 * 
	 * @param Modelo $obj 
	 * @throws PDOException
	 */
	public function insert(Modelo $obj){
	    try{
    		$this->startTransaction();

    		    $this->tipoOperacao = "INSERT";
        		$this->setParams($obj);
        		$this->prepareStatement($obj);
        		$this->bindParams();
        		$erro = $this->statement->execute();
        		$this->statement->errorInfo();
        		$obj->setIncrementId($this->con->lastInsertId());
    		
    		$this->encerrarQuery();
	    }catch(\PDOException $e){
	        throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
	    }
	}
	
	public function update(Modelo $obj){
	    try{
    		$this->startTransaction();

    		    $this->tipoOperacao = "UPDATE";
        		$this->setParams($obj,true);
        		$call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ", $obj->getPrimaryKey())));
        		$id = $obj->$call();
        	    $this->QueryBuilder->addWhereEquals($obj->getPrimaryKey(), $id);
        		$this->prepareStatement($obj);
        		$this->bindParams();
        		$erro = $this->statement->execute();
        		$this->statement->errorInfo();	    
    		
    		$this->encerrarQuery();
	    }catch(\PDOException $e){
	        throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
	    }
	}
	
	public function delete(Modelo $obj){
	    try{
    		$this->startTransaction();

    		    $this->tipoOperacao = "DELETE";
        		$this->setParams($obj,true);
        		$call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ", $obj->getPrimaryKey())));
        		$id = $obj->$call();
        	    $this->QueryBuilder->addWhereEquals($obj->getPrimaryKey(), $id);
        		$this->prepareStatement($obj);
        		$this->bindParams();
        		$erro = $this->statement->execute();
        		$this->statement->errorInfo();	    
    		
    		$this->encerrarQuery();
	    }catch(\PDOException $e){
	        throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
	    }
	}
	

	protected function query($sql){
	    try{
    		$this->startTransaction();
    		
    		    $return = array();
    		    $this->statement = $this->con->prepare($sql);
        		$this->statement->execute();
        		while($row = $this->statement->fetch(\PDO::FETCH_ASSOC)){
        			$return[] = $row;
        		}
        		
    		$this->encerrarQuery();
    		return $return;
	    }catch(\PDOException $e){
	        throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
	    }
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