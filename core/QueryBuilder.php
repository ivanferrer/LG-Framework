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
    private $campo1;
    private $campo2;
    private $camposSet;
    private $listCampo;
    private $echoQuery;

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
        $this->campo1 = array();
        $this->campo2 = array();
        $this->camposSet = array();
        $this->listCampo = array();
    }
    //para imprimir a query trabalhada.
    public function getPrintQuery(){
         $this->echoQuery = true;
    }

    public function setOr($campo1,$campo2){
    	$this->campo1[$campo1] = $campo1;
    	$this->campo2[$campo2] = $campo2;
    	$this->camposSet[$campo1] = array($campo1,$campo2);
    }
    
    public function addWhereEquals($campo, $valor, $operador = "AND", $agrupamento = 0) {
        if ($valor != "") {
            $whr = ":where_eq_".preg_replace("/[^A-Za-z]/",'',$campo);
            //$this->query[$operador][$agrupamento][] = "$campo = :where_eq_$campo";
            $this->query[] = "$campo = $whr";
            $this->parametroWhere[$whr] = $valor;
            $this->listCampo[]=$campo;
            return $campo;
            
        }
    }

    public function addWhereDifferent($campo, $valor) {
        if ($valor != "") {
            $whr = ":where_df_".preg_replace("/[^A-Za-z]/",'',$campo);
            $this->query[] = "$campo != $whr";
            $this->parametroWhere[$whr] = $valor;
            $this->listCampo[]=$campo;
            return $campo;
        }
    }

    public function addWhereLike($campo, $valor) {
        if ($valor != "") {
            $whr = ":where_lk_".preg_replace("/[^A-Za-z]/",'',$campo);
            $this->query[] = "$campo LIKE '%$whr%'";
            $this->parametroWhere[$whr] = $valor;
            $this->listCampo[]=$campo;
            return $campo;
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
            $this->listCampo[]=$campo;
            return $campo;
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
            $this->listCampo[]=$campo;
            return $campo;
        }
    }

    public function addWhereBetween($campo, $start, $end) {

        $whr = ":where_dt_".preg_replace("/[^A-Za-z]/",'',$campo);
        $this->query[] = "$campo between '" . $whr . "_start' AND '".$whr."_end'";
        $this->parametroWhere[$whr.'_start'] = $start;
        $this->parametroWhere[$whr.'_end'] = $end;
        $this->listCampo[]=$campo;
        return $campo;
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
                    'Parâmetro $orientacao do addOrderBy() deve ser "ASC" ou "DESC"');
        }
    }
    
    public function setLimit($limit, $inicio = null){
        if(!is_int($limit) || (!is_null($inicio) && !is_int($limit))){
            throw new e\DaoException("Cláusula 'LIMIT' deve ser do tipo INTEGER");
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

    if(count($this->query) != 0){
			$query .=" WHERE ";
			
			$totalCampos = count($this->listCampo);
			$total_query = count($this->query);
			    // sort($this->query);
				
					$x=-1;
			         $y=0;	  
				  foreach($this->query as $key => $strQuery){
						  $x++;
						  $y++;
						  $query .=$strQuery;
						 $operador = ($this->camposSet[$this->listCampo[$x]][0] == $this->listCampo[$x]) ? " OR " : " AND ";
						   if(($operador == " OR " && $this->camposSet[$this->listCampo[$x]][1] == $this->listCampo[$y]) || $operador == " AND " || $operador == ""){
						     $query .= ($total_query - 1 > $x ) ? $operador : "";
						   }
						   else
						   {
						   	$erroMsg = '<br>Reposicione-os de maneira sequencial.';
							   	if($this->camposSet[$this->listCampo[$x]][0]==""){
							   		$this->camposSet[$this->listCampo[$x]][0] = 'Campo(1) Nulo';
							   		$erroMsg = '<br>Defina os dois campos onde deve haver a condição "OR".';
							   	}
							   	if($this->camposSet[$this->listCampo[$x]][1]==""){
							   		$this->camposSet[$this->listCampo[$x]][1] = 'Campo(2) Nulo';
							   		$erroMsg = '<br>Defina os dois campos onde deve haver a condição "OR".';
							   	}
								throw new e\DaoException('Não é possível atribuir a condição "OR" para os campos "'.$this->camposSet[$this->listCampo[$x]][0].'" e "'.$this->camposSet[$this->listCampo[$x]][1].'".'.$erroMsg);
						   }
				       }
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
        
        if($this->echoQuery){
        	//para ver a query na página:
            echo "<div style=\"background:#eee!Important; display:block!Important; padding:10px 6px!Important; border:1px solid #ccc!Important; font-family:Courier New!Important; position:fixed!Important; width:78%!Important; left:50%!Important; margin-left:-41%!Important; z-index:999999!Important; font-size:14px; margin-top:5px;\">".$query."</div>"; 
           }
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
                throw new e\DaoException('Parâmetro $operacao deve ser SELECT, UPDATE, INSERT ou DELETE.');
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
