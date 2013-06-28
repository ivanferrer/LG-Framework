<?php
namespace exceptions;

use core\Modelo;

use core as c;
class ErrorMessages{
    
    private $code;
    private $message;
    private $file;
    private $line;
    private $trace;
    private $previous;
    private $exception;
    private $relacaoTabela;
    private $relacaoCampo;
    private $constraint;
    private $model;
    
    public function __construct(\Exception $exception,$model = null){
        $this->exception = $exception;
        $this->message = $exception->getMessage();
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();
        $this->previous = $exception->getPrevious();
        $this->model = $model;
    }
    
    public function getMessage(){
        $this->getModelRelations();
        $mensagem = $this->getConfMessage();
        if(!$mensagem){
            $mensagem = $this->getLGFMessage();
        }
        return $mensagem;
    }
    
    private function getLGFMessage(){
        switch ($this->code){
            case 0 : return $this->message;
            case 1 : return "Erro desconhecido";
            case 1002 : return "Ocorreu um erro no site. Entre em contato com o suporte.";
            case 404 : return "Página Não Encontrada";
            //case 1452: 
            //constraint fails
            case 1062:    return "Registro já existe.";
            default: return "Erro Não Identificado, código: ".$this->code;
        }
    }
    
    private function getConfMessage(){
        $file = APP_DIR.DS."config".DS."erros.conf";
        $encontrado = false;
        if(file_exists($file)){
            $linhas = file($file);
            foreach($linhas as $linha){
                $info = explode("=", $linha);
                $codigo = trim($info[0]);
                if($this->code == $codigo){
                    $encontrado = $this->tratarMensagemConf(trim($info[1]));
                }
            }
        }
        return $encontrado;
    }
    
    private function tratarMensagemConf($mensagem){
        $mensagem = str_replace("{relacao-campo}",$this->relacaoCampo,$mensagem);
        $nome = (strstr($this->relacaoCampo,"_")) ? ucwords(str_replace("_"," ",strstr($this->relacaoCampo,"_"))) : ucfirst($this->relacaoCampo);
        $mensagem = str_replace("{relacao-campo-formatado}",trim($nome),$mensagem);
        $mensagem = str_replace("{constraint}",$this->constraint,$mensagem);
        $mensagem = str_replace("{relacao-tabela}",$this->relacaoTabela,$mensagem);
        return $mensagem;
    }
    
    private function getModelRelations(){
        if($this->model instanceof Modelo){
            $resp = false;
            $constraints = $this->model->getChavesRelacionais();    
            foreach($constraints as $campo => $cnst){
                if(strstr($this->message,$cnst)){
                    $this->constraint = $cnst;
                    $this->relacaoCampo = $campo;
                    $temp = explode("_FK_", $cnst);
                    $this->relacaoTabela = $temp[1];
                    $resp = true;
                }
            }
            if(!$resp){
                $constraints = $this->model->getChavesUnicas();    
                foreach($constraints as $campo => $cnst){
                    if(strstr($this->message,$cnst)){
                        $this->constraint = $cnst;
                        $this->relacaoCampo = $campo;
                    }
                }
            }
        }
    }
    
}