<?php
namespace exceptions;
class ExceptionHandler{
    
    private $exception;
    private $codigoErro;
    private $trace;
    private $model;
    
    private function __construct(\Exception $exception, $codigoErro = false){
        $this->exception = $exception;
        if(method_exists($exception, "getModel")){
            $this->model = $exception->getModel();
        }
        $this->codigoErro = ($codigoErro) ? $codigoErro : $exception->getCode();
        if(DEBUG_LIGADO){ $this->debug();}
    }
    
    public static function tratarErro(\Exception $e,$codigo = false){
        if($codigo === false){
            $codigo = $e->getCode();
        }
        $_ExHandler = new ExceptionHandler($e,$codigo);
        $_ExHandler->registraLog();
        $previous = $_ExHandler->exception->getPrevious();
        while($previous != null){
            $_ExHandler->registraLog($previous);
            $previous = $previous->getPrevious();
        }
        if (extension_loaded('newrelic')){
            newrelic_notice_error($_ExHandler->getMensagem($e),$e);
        }
        return $_ExHandler->getMensagem($e).$_ExHandler->trace;
    }
    
    private function debug(){
        $this->trace = "<br><pre style='text-align:left'>";
        $this->trace.= "Mensagem Usuário: ".$this->getMensagem($this->exception)."<br><br>";
        $this->trace.= "Mensagem Sistema: ".$this->exception->getMessage()."<br><br>";
        $this->trace.= "Código: [ {$this->exception->getCode()} ]<br>";
        $this->trace.= "Linha: [ {$this->exception->getLine()} ]<br>";
        $this->trace.= "Arquivo: ".$this->exception->getFile()."<br><br>";
        $this->trace.= "Trace:<br>";
        $this->trace.= print_r($this->exception->getTraceAsString(),true);
        $this->trace.= "</pre><br>";
    }
    
    private function getMensagem($e){
        $msg = new ErrorMessages($e,$this->model);
        return $msg->getMessage();
    }
    
    
    /**
     * Salvar os erros na pasta log
     */
    private function registraLog($exception = null) {
        $e = (is_null($exception)) ? $this->exception : $exception;
        //Define o arquivo que será criado ou gravado caso exista
        $log = APP_DIR."log".DS."error_log_" . date("Ymd") . ".txt";
    
        //Se não existe arquivo, cria
        if( !file_exists($log)){
    
            $msg = "/************************************************** " . "\r\n".
                    " * Arquivo de log " ."\r\n".
                    " * " ."\r\n".
                    " * Data Criação: " . date("d/m/Y H:i") . " " ."\r\n".
                    " *************************************************/ " ."\r\n";
    
        }else{
            $msg = "";
        }
        //Abre o arquivo
        if($fp = fopen( $log , "a+")) {
            $trace = $e->getTraceAsString();
            $trace = explode("#", $trace);
            $trace = implode("\r\n", $trace);
            $usuario = (isset($_SESSION['LGF']['identidade'])) ? $_SESSION['LGF']['identidade']  : "não está logado";
            $usuario = (is_array($usuario)) ? $usuario[0] : $usuario;
            //Arquivo
            $msg .= (is_null($exception)) ? "————————————————————————————————————————————————————————————————————————————————————————————" : "\r\n***** LANÇADO POR: ";
            $msg .= "\r\n"."\r\n";
            $msg .= "Tipo: ".str_replace("exceptions\\","",get_class($e))." | Usuario ID: $usuario"." | Request: ".$_SERVER['REQUEST_URI']."\r\n";
            $msg .= "Hora: ".date("d/m/Y - H:i:s") . " " ."\r\n";
            $msg .= "Erro [ {$e->getCode()} ]: ".$e->getFile()." ( Linha: {$e->getLine()} ) " ."\r\n";
            $msg .= "Mensagem Usuário:{$this->getMensagem($e)} " ."\r\n";
            $msg .= "Mensagem Interna:{$e->getMessage()} " ."\r\n";
            $msg .= "Trace: "."\r\n";
            $msg .= "{".$trace."} " ."\r\n";
    
                    //Grava
                fwrite($fp, $msg);
            fclose($fp);
        }
    
    }
}
?>