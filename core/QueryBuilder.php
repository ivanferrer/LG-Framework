<?php
namespace core;
use exceptions as e;

class QueryBuilder extends ConnectionFactory {

    private $modelo;
    private $operacao;
    private $campos;
    private $valores;
    private $query;
    private $tabela;
    private $order;
    private $orderOrientacao;
    private $group;

    public function __construct() {
        $this->modelo = null;
        $this->operacao = null;
        $this->campos = array();
        $this->valores = array();
        $this->query = array();
        $this->tabela = array();
        $this->order = array();
        $this->orderOrientacao = null;
        $this->group = array();
    }

    public function addWhereEquals($campo, $valor) {
        if ($valor != "") {
            $valor = (is_string($valor)) ? "'".$valor."'" : $valor;
            $this->query[] = "$campo = $valor";
        }
    }

    public function addWhereDifferent($campo, $valor) {
        if ($valor != "") {
            $valor = (is_string($valor)) ? "'".$valor."'" : $valor;
            $this->query[] = "$campo != $valor";
        }
    }

    public function addWhereLike($campo, $valor) {
        if ($valor != "") {
            $this->query[] = "$campo LIKE '%$valor%'";
        }
    }

    public function addWhereIn($campo, $valor) {
        if (count($valor) > 0 && $valor[0] != '') {
            foreach($valor as &$value){
                $value = (is_string($value)) ? "'".$value."'" : $value;
            }
            $valor = implode(",", $valor);
            $this->query[] = "$campo in('$valor')";
        }
    }

    public function addWhereNotIn($campo, $valor) {
        if (count($valor) > 0 && $valor[0] != '') {
            foreach($valor as &$value){
                $value = (is_string($value)) ? "'".$value."'" : $value;
            }
            $valor = implode(",", $valor);
            $this->query[] = "$campo not in('$valor')";
        }
    }

    public function addWhereBetween($campo, $start, $end) {
        $this->query[] = "$campo between '" . $start . "' AND '" . $end . "'";
    }

    public function addWhereCustom($customWhere) {
        $this->query[] = $customWhere;
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

    public function addGroupBy($campo) {
        if (is_array($campo))
            $campo = implode(",", $campo);
        $this->group[] = $campo;
    }

    public function getQuery(Modelo $modelo, $operacao) {
        $this->modelo = $modelo;
        $this->tabela = $this->modelo->getTabela();
        $this->operacao = strtoupper($operacao);

        $query = $this->iniciarQuery();

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
        $this->__construct();
        return $query;
    }

    private function gerarOperacao() {
    }

    private function iniciarQuery() {
        switch($this->operacao) {
            case 'SELECT':
                $campos = (count($this->campos) > 0) ? implode(",", $this->campos) : "*";
                $query =  "SELECT $campos FROM $this->tabela";
                break;
            case 'UPDATE':
                $query =  "UPDATE $this->tabela";
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

    public function setCamposValores(array $campos,array $valores) {
        $this->campos = $campos;
    }
}
