<?php
namespace exceptions;

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
    private $relacaoConstraint;
    private $modelo;
    
    public function __construct(\Exception $exception){
        $this->exception = $exception;
        $this->message = $exception->getMessage();
        $this->code = $exception->getCode();
        $this->file = $exception->getFile();
        $this->line = $exception->getLine();
        $this->trace = $exception->getTrace();
        $this->previous = $exception->getPrevious();
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
			case 0 : return "Ocorreu um problema na execução do Script.";
			case 1 : return "Erro de SQL";
			case 1002 : return "Ocorreu um erro no site. Entre em contato com o suporte.";
			case 404 : return "Página Não Encontrada";
			//case 1452: 
			//constraint fails
			case 1062:	return "Registro já existe.";
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
        $mensagem = str_replace("{relacao-constraint}",$this->relacaoConstraint,$mensagem);
        $mensagem = str_replace("{relacao-tabela}",$this->relacaoTabela,$mensagem);
        return $mensagem;
    }
    
    private function getModelRelations(){
        $obj = $this->findModel($this->trace);
        if(is_null($obj)){
            return 'nada';
        }
        $constraints = $obj->getChavesRelacionais();
        foreach($constraints as $campo => $cnst){
            if(strstr($this->message,$cnst)){
                $this->relacaoConstraint = $cnst;
                $this->relacaoCampo = $campo;
                $temp = explode("_FK_", $cnst);
                $this->relacaoTabela = $temp[1];
            }
        }
    }
    
    private function findModel($trace){
        if(is_array($trace)){
            foreach($trace as $map){
                $result = $this->findModel($map);
                if($result instanceof c\Modelo){
                    return $result;
                }
            }
        }else{
            if($trace instanceof c\Modelo){
                return $trace;
            }
        }
    }
}