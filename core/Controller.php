<?php
namespace core;
use lib as l;
use exceptions as e;
abstract class Controller {
    private $metodosAutenticados;
    private $metodosNaoAutenticados;
    private $metodosAcessiveisPorUrl;
    private $metodosNaoAcessiveisPorUrl;
    protected $generalContent;
    protected $template;

    public function __construct() {
        $this->metodosAutenticados = array();
        $this->metodosNaoAutenticados = array();
        $this->metodosAcessiveisPorUrl = array();
        $this->metodosNaoAcessiveisPorUrl = array();
    }

    protected function setMetodosAutenticados($valor) {
        $this->metodosAutenticados = $valor;
    }

    protected function setMetodosNaoAutenticados($valor) {
        $this->metodosNaoAutenticados = $valor;
    }

    public function setMetodosAcessiveisPorUrl($valor) {
        $this->metodosAcessiveisPorUrl = $valor;
    }

    public function setMetodosNaoAcessiveisPorUrl($valor) {
        $this->metodosNaoAcessiveisPorUrl = $valor;
    }

    public function getMetodosAutenticados() {
        return $this->metodosAutenticados;
    }

    public function getMetodosNaoAutenticados() {
        return $this->metodosNaoAutenticados;
    }

    public function getMetodosAcessiveisPorUrl() {
        return $this->metodosAcessiveisPorUrl;
    }

    public function getMetodosNaoAcessiveisPorUrl() {
        return $this->metodosNaoAcessiveisPorUrl;
    }

    public function listar() {
        return $this->dao->select($this->model);
    }

    public function inserir($json = false) {
        try {
            if (!($this->model instanceof Modelo)) {
                throw new \Exception(
                        'Não foi definido o atributo $this->model no Controller.');
            }
            Globals::setAlertTipo("alert");
            $arr = $_POST;
            if ($json) {
                foreach ($_POST as $key => $value) {
                    $arr[$key] = utf8_decode($value);
                }
            }
            l\Functions::setObjectFromArray($this->model, $arr);

            $this->dao->insert($this->model);

            $json_resp['id'] = $this->model->getAutoIncrementId();

            Globals::setAlertTipo("success");
            $nome = explode('\\', get_class($this));
            Globals::setAlertMensagem(end($nome) . " inserido com sucesso!");
            $this->dao->commit();
        } catch (\Exception $e) {
            Globals::setAlertTipo("error");
            Globals::setAlertMensagem($e->getMessage());
            $this->dao->rollBack();
            e\ExceptionHandler::tratarErro($e);
        }
        if ($json) {
            $json_resp['status'] = Globals::getAlertTipo();
            $json_resp['mensagem'] = Globals::getAlertMensagem();
            //echo json_encode($json_resp);
            echo l\Functions::toJson($json_resp);
        } else {
            if(isset($_POST['LGF_url_callback'])){
                header("Location: " . $_POST['LGF_url_callback'], true, 307);
            }else{
                $anterior = Globals::getAnterior();
                header("Location: " . HTTP_PATH . $anterior['urlChamada'], true, 307);
            }
        }
    }

    public function alterar($json = false) {
        try {
            Globals::setAlertTipo("alert");
            $arr = $_POST;
            if ($json) {
                foreach ($_POST as $key => $value) {
                    $arr[$key] = utf8_decode($value);
                }
            }
            l\Functions::setObjectFromArray($this->model, $arr);
            $this->dao->update($this->model);
            Globals::setAlertTipo("success");
            $nome = explode('\\', get_class($this));
            Globals::setAlertMensagem(end($nome) . " alterado com sucesso!");
            $this->dao->commit();
        } catch (\Exception $e) {
            Globals::setAlertTipo("error");
            Globals::setAlertMensagem(e\ExceptionHandler::tratarErro($e));
            $this->dao->rollBack();
            e\ExceptionHandler::tratarErro($e);
        }

        if ($json) {
            $json_resp['status'] = Globals::getAlertTipo();
            $json_resp['mensagem'] = utf8_encode(Globals::getAlertMensagem());
            echo l\Functions::toJson($json_resp);
        } else {
            if(isset($_POST['LGF_url_callback'])){
                header("Location: " . $_POST['LGF_url_callback'], true, 307);
            }else{
                $anterior = Globals::getAnterior();
                header("Location: " . HTTP_PATH . $anterior['urlChamada'], true, 307);
            }
        }
    }

    public function excluir($json = false) {
        try {
            if (!isset($_POST)) {
                throw new \Exception(
                        "Não foi informado parâmetro para a exclusão.");
            }
            Globals::setAlertTipo("alert");
            $arr = $_POST;
            if ($json) {
                foreach ($_POST as $key => $value) {
                    $arr[$key] = utf8_decode($value);
                }
            }
            l\Functions::setObjectFromArray($this->model, $arr);

            $this->dao->delete($this->model);

            Globals::setAlertTipo("success");
            $nome = explode('\\', get_class($this));
            Globals::setAlertMensagem(end($nome) . " excluído com sucesso!");
            $this->dao->commit();
        } catch (\Exception $e) {
            Globals::setAlertTipo("error");
            Globals::setAlertMensagem($e->getMessage());
            $this->dao->rollBack();
            e\ExceptionHandler::tratarErro($e);
        }

        if ($json) {
            $json_resp['status'] = Globals::getAlertTipo();
            $json_resp['mensagem'] = Globals::getAlertMensagem();
            echo l\Functions::toJson($json_resp);
        } else {
            if(isset($_POST['LGF_url_callback'])){
                header("Location: " . $_POST['LGF_url_callback'], true, 307);
            }else{
                $anterior = Globals::getAnterior();
                header("Location: " . HTTP_PATH . $anterior['urlChamada'], true, 307);
            }
        }
    }

    /*
    public function setAlerta($mensagem,$classe){
        $this->alertaMensagem = $mensagem;
        $this->alertaClasse = $classe;
        $_SESSION['LGF_alerta']['mensagem'] = $mensagem;
        $_SESSION['LGF_alerta']['classe'] = $classe;
    }*/

}
