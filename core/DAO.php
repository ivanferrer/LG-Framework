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
    private $paramsWhere;
    private $finalQuery;
    protected $QueryBuilder;
    
    public function __construct(){
        try{@$this->con = new \PDO(null, null, null, null);}catch(\PDOException $e){} // hack para IDE interpretar $this->con como objeto PDO
        $this->QueryBuilder = new QueryBuilder();
        $this->campos = array();
        $this->valores = array();
        $this->operacaoMultipla = false;
        $this->preparedStatus = false;
        $this->paramsWhere = array();
    }
    
    private function prepareStatement($obj){
        if(!$this->preparedStatus){
            if(!$this->QueryBuilder->issetCampos()){
                $this->QueryBuilder->setCamposValores($this->campos, $this->valores);
            }
            $this->paramsWhere = $this->QueryBuilder->getParametroWhere();
            $sql = $this->QueryBuilder->getQuery($obj, $this->tipoOperacao);
            $this->finalQuery = $sql;
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
            if(!is_null($value) && $this->tipoOperacao != 'SELECT'){
                if($ignoreKey){
                    $pk = $obj->getChavePrimaria();
                    if(is_array($pk)){
                        if(!in_array($campo,$pk)){
                            $this->campos[$key] = $campo;
                            $this->valores[$key] = $value;
                        }
                    }else{
                        if($campo != $pk){
                            $this->campos[$key] = $campo;
                            $this->valores[$key] = $value;
                        }
                    }
                }else{
                    $this->campos[$key] = $campo;
                    $this->valores[$key] = $value;
                }
            }
        }
    }
    
    private function bindParams(){
        if(count($this->paramsWhere) > 0){
            foreach($this->paramsWhere as $key => $value){
                $val = $this->statement->bindValue($key,$value);
                $this->finalQuery = str_replace($key,$value,$this->finalQuery);
            }
        }
        foreach($this->campos as $key => $value){
            if($this->tipoOperacao != 'DELETE'){
                $this->finalQuery = str_replace($key,$value,$this->finalQuery);
                $this->statement->bindValue($value, $this->valores[$key]);
            }
        }
    }
    
    public function getId($id, Modelo $obj = null){
        $obj = (is_null($obj)) ? $this->modelo : $obj;
        try{
            $ids = $obj->getValorChavePrimaria();
            if(is_array($ids)){
                foreach($ids as $key=>$value){
                    $this->QueryBuilder->addWhereEquals($key, $value);
                }
            }else{
                $this->QueryBuilder->addWhereEquals($obj->getChavePrimaria(), $id);
            }
            $list = $this->select($obj);
            $list->rewind();
            return $list->current();
        }catch(\PDOException $e){
            if(isset($e->errorInfo[2])){
                throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
            }else{
                throw new e\DaoException($e->getMessage());
            }
        }
    }

    protected function getMax(m\Modelo $obj, $campo){
    
    }

    protected function getMin(m\Modelo $obj, $campo){
    
    }

    protected function getSum(m\Modelo $obj, $campo){
    
    }
    
    
    /**
     * @param Modelo $obj
     * @return SplObjectStorage (Lista/Array de Objetos)
     * @throws \PDOException
     */
    public function select(Modelo $obj = null,$retornoGenerico = false){
        $obj = (is_null($obj)) ? $this->modelo : $obj;
        try{
            $this->startTransaction();

                $this->tipoOperacao = "SELECT";
                $list = new \SplObjectStorage();
                $this->setParams($obj);
                $this->prepareStatement($obj);
                $this->bindParams();
                $this->statement->execute();
                $classReturned = ($retornoGenerico) ? 'stdClass' : get_class($obj);
                $this->statement->setFetchMode(\PDO::FETCH_CLASS|\PDO::FETCH_PROPS_LATE, $classReturned);
                while($obj = $this->statement->fetch()){
                    $list->attach($obj);
                }
            $this->encerrarQuery();
            return $list;
        }catch(\PDOException $e){
            if(isset($e->errorInfo[2])){
                throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
            }else{
                throw new e\DaoException($e->getMessage());
            }
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
                $obj->setAutoIncrementId($this->con->lastInsertId());
            
            $this->encerrarQuery();
        }catch(\PDOException $e){
            if(isset($e->errorInfo[2])){
                throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e,$obj);
            }else{
                throw new e\DaoException($e->getMessage(),$e->getCode(),$e,$obj);
            }
        }
    }
    
    public function update(Modelo $obj){
        try{
            $this->startTransaction();

                $this->tipoOperacao = "UPDATE";
                $this->setParams($obj,true);
                //$call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ", $obj->getChavePrimaria())));
                //$id = $obj->$call();
                $id = $obj->getValorChavePrimaria();
                if(is_array($id)){
                    foreach($id as $key=>$value){
                        $this->QueryBuilder->addWhereEquals($key, $value);
                    }
                }else{
                    $this->QueryBuilder->addWhereEquals($obj->getChavePrimaria(), $id);
                }
                $this->prepareStatement($obj);
                $this->bindParams();
                $erro = $this->statement->execute();
            
            $this->encerrarQuery();
        }catch(\PDOException $e){
            if(isset($e->errorInfo[2])){
                throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
            }else{
                throw new e\DaoException($e->getMessage());
            }
        }
    }
    
    public function delete(Modelo $obj){
        try{
            $this->startTransaction();

                $this->tipoOperacao = "DELETE";
                $this->setParams($obj,true);
                //$call = 'get'.str_replace(" ","",ucwords(str_replace("_"," ", $obj->getChavePrimaria())));
                //$id = $obj->$call();
                $id = $obj->getValorChavePrimaria();
                if(is_array($id)){
                    foreach($id as $key=>$value){
                        $this->QueryBuilder->addWhereEquals($key, $value);
                    }
                }else{
                    $this->QueryBuilder->addWhereEquals($obj->getChavePrimaria(), $id);
                }
                $this->prepareStatement($obj);
                $this->bindParams();
                $this->statement->execute();
            
            $this->encerrarQuery();
        }catch(\PDOException $e){
            if(isset($e->errorInfo[2])){
                throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
            }else{
                throw new e\DaoException($e->getMessage());
            }
        }
    }
    
    protected function query($sql){
        try{
            $this->startTransaction();
            
                $return = array();
                $this->statement = $this->con->prepare($sql);
                $this->statement->execute();
                if(stripos("select", $sql) == 0){
                    while($row = $this->statement->fetch(\PDO::FETCH_ASSOC)){
                        $return[] = $row;
                    }
                }
            $this->encerrarQuery();
            return $return;
        }catch(\PDOException $e){
            if(isset($e->errorInfo[2])){
                throw new e\DaoException($e->errorInfo[2],$e->errorInfo[1],$e);
            }else{
                throw new e\DaoException($e->getMessage());
            }
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
            $this->QueryBuilder = new QueryBuilder();
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
                $this->stopOperacaoMultipla();
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