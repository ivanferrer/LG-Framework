<?php
namespace core;
use exceptions as e;

class QueryBuilder extends ConnectionFactory {

    private $modelo;
    private $operacao;
    private $campos;
    private $valores;
    private $join;
    private $query;
    private $parametroWhere;
    private $tabela;
    private $order;
    private $orderOrientacao;
    private $group;
    private $limit;

    public function __construct() {
        $this->modelo = null;
        $this->operacao = null;
        $this->campos = array();
        $this->valores = array();
        $this->query = array();
        $this->parametroWhere = array();
        $this->tabela = array();
        $this->order = array();
        $this->orderOrientacao = null;
        $this->group = array();
        $this->limit = null;
    }

    public function addWhereEquals($campo, $valor, $operador = "AND", $agrupamento = 0) {
        if ($valor != "") {
            $whr = ":where_eq_".preg_replace("/[^A-Za-z]/",'',$campo);
            //$this->query[$operador][$agrupamento][] = "$campo = :where_eq_$campo";
            $this->query[] = "$campo = $whr";
            $this->parametroWhere[$whr] = $valor;
            
        }
    }

    public function addWhereDifferent($campo, $valor) {
        if ($valor != "") {
            $whr = ":where_df_".preg_replace("/[^A-Za-z]/",'',$campo);
            $this->query[] = "$campo != $whr";
            $this->parametroWhere[$whr] = $valor;
        }
    }

    public function addWhereLike($campo, $valor) {
        if ($valor != "") {
            $whr = ":where_lk_".preg_replace("/[^A-Za-z]/",'',$campo);
            $this->query[] = "$campo LIKE '%$whr%'";
            $this->parametroWhere[$whr] = $valor;
        }
    }

    public function addWhereIn($campo, $valor) {
        if (count($valor) > 0 && $valor[0] != '') {
            foreach($valor as &$value){
                $value = (is_string($value)) ? "'".$value."'" : $value;
            }
            $valor = implode(",", $valor);
            $whr = ":where_in_".preg_replace("/[^A-Za-z]/",'',$campo);
            $this->query[] = "$campo in('$whr')";
            $this->parametroWhere[$whr] = $valor;
        }
    }

    public function addWhereNotIn($campo, $valor) {
        if (count($valor) > 0 && $valor[0] != '') {
            foreach($valor as &$value){
                $value = (is_string($value)) ? "'".$value."'" : $value;
            }
            $valor = implode(",", $valor);
            $whr = ":where_ni_".preg_replace("/[^A-Za-z]/",'',$campo);
            $this->query[] = "$campo not in('$whr')";
            $this->parametroWhere[$whr] = $valor;
        }
    }

    public function addWhereBetween($campo, $start, $end) {

        $whr = ":where_dt_".preg_replace("/[^A-Za-z]/",'',$campo);
        $this->query[] = "$campo between '" . $whr . "_start' AND '".$whr."_end'";
        $this->parametroWhere[$whr.'_start'] = $start;
        $this->parametroWhere[$whr.'_end'] = $end;
    }

    public function addWhereCustom($customWhere) {
        $this->query[] = $customWhere;
    }
    
    public function join($tabela, $as, $on){
        $this->join[] = array('tabela'=>$tabela,'as'=>$as,'on'=>$on);
    }

    public function addGroupBy($campo) {
        if (is_array($campo))
            $campo = implode(",", $campo);
        $this->group[] = $campo;
    }

    public function addOrderBy($campo, $orientacao = 'ASC') {
        if (is_array($campo)) {
            $campo = implode(",", $campo);
        }
        $this->order[] = $campo;

        if ($orientacao == 'ASC') {
            $this->orderOrientacao = "ASC";
        }
        elseif ($orientacao == 'DESC') {
            $this->orderOrientacao = "DESC";
        }
        else {
            throw new e\DaoException(
                    'Par�metro $orientacao do addOrderBy() deve ser "ASC" ou "DESC"');
        }
    }
    
    public function setLimit($limit, $inicio = null){
        if(!is_int($limit) || (!is_null($inicio) && !is_int($limit))){
            throw new e\DaoException("Cl�usula 'LIMIT' deve ser do tipo INTEGER");
        }
        $this->limit = $limit;
        if(!is_null($inicio)){
            $this->limit = $inicio.','.$limit;
        }
    }

    public function getQuery(Modelo $modelo, $operacao) {
        $this->modelo = $modelo;
        $this->tabela = $this->modelo->getTabelaDoModelo();
        $this->operacao = strtoupper($operacao);

        $query = $this->iniciarQuery();
        if($operacao == "SELECT" && count($this->join) > 0 ){
            foreach($this->join as $key => $join){
                $query.= " JOIN ".$join['tabela']." ".$join['as'];
                $query.= " ON ".$join['on'];
            }
            
        }

        if (count($this->query) != 0) {
            $query .= " WHERE ";
            $query .= implode(" AND ", $this->query);
        }
        if (count($this->group) != 0) {
            $query .= " group by " . implode(",", $this->group);
        }
        if (count($this->order) != 0) {
            $query .= " order by " . implode(",", $this->order);
            $query .= " " . $this->orderOrientacao;
        }
        if (!is_null($this->limit)) {
            $query .= " LIMIT " . $this->limit;
        }
        $this->__construct();
        return $query;
    }

    public function getParametroWhere() {
        return $this->parametroWhere;
    }

    private function iniciarQuery() {
        switch($this->operacao) {
            case 'SELECT':
                $campos = (count($this->campos) > 0) ? implode(",", $this->campos) : "*";
                $query =  "SELECT $campos FROM $this->tabela ".substr($this->tabela, 0, 1);
                break;
            case 'UPDATE':
                foreach($this->campos as $campo){
		            $pk = $this->modelo->getChavePrimaria();
		            if(is_array($pk)){
		                if(!in_array($campo,$pk)){
		                    $campos[] = $campo." = :".$campo;
		                }
		            }else{
    		            if($campo != $pk){
    		                $campos[] = $campo." = :".$campo;
    		            }
		            }
                }
                $query =  "UPDATE $this->tabela SET ".implode(", ",$campos);
                break;
            case 'INSERT':
                $query =  "INSERT INTO " . $this->tabela . " ("
                        . implode(",", $this->campos) . ") values(:"
                        . implode(",:", $this->campos) . ")";
                break;
            case 'DELETE':
                $query =  "DELETE FROM $this->tabela";
                break;
            default:
                throw new e\DaoException('Par�metro $operacao deve ser SELECT, UPDATE, INSERT ou DELETE.');
        }
        return $query;
    }

    public function setCamposValores(array $campos,array $valores = null) {
        $this->campos = array();
        $this->campos = $campos;
        $this->valores = array();
        $this->valores = $valores;
    }
    
    public function issetCampos(){
        return (count($this->campos) > 0);
    }
}
