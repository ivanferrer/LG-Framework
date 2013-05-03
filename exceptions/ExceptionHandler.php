<?php
namespace exceptions;
class ExceptionHandler{
	
	private $exception;
	private $codigoErro;
	private $trace;
	
	private function __construct(\Exception $exception, $codigoErro = false){
		$this->exception = $exception;
		$this->codigoErro = ($codigoErro) ? $codigoErro : $exception->getCode();
		if(DEBUG_LIGADO){ $this->debug();}
	}
	
	public static function tratarErro(\Exception $e,$codigo = false){
		if($codigo === false){
			$codigo = $e->getCode();
		}
		$_ExHandler = new ExceptionHandler($e,$codigo);
		$_ExHandler->registraLog();
		if (extension_loaded('newrelic')){
		    newrelic_notice_error($_ExHandler->getMensagem($e->getCode()),$e);
		}
		return $_ExHandler->getMensagem($e->getCode())."<br>".$_ExHandler->trace;
	}
	
	private function debug(){
		$this->trace = "<pre style='text-align:left'>";
		$this->trace.= "Mensagem Usuário: ".$this->getMensagem($this->exception->getCode())."<br><br>";
		$this->trace.= "Mensagem Sistema: ".$this->exception->getMessage()."<br><br>";

		$this->trace.= "Código: [ {$this->exception->getCode()} ]<br>";
		$this->trace.= "Linha: [ {$this->exception->getLine()} ]<br>";
		$this->trace.= "Arquivo: ".$this->exception->getFile()."<br><br>";
		$this->trace.= "Trace:<br>";
		$this->trace.= print_r($this->exception->getTraceAsString(),true);
		$this->trace.= "</pre>";
	}
	
	private function getMensagem($codigoErro){
		switch ($codigoErro){
			case 0 : return "Ocorreu um problema na execução do Script.";
			case 1 : return "Erro de SQL";
			case 1002 : return "Ocorreu um erro no site. Entre em contato com o suporte.";
			case 404 : return "Página Não Encontrada";
			case 1062:	return "Registro já existe.";
			default: return "Erro Não Identificado, código: ".$codigoErro;
		}	
	}
	
	
	/**
	 * Salvar os erros na pasta log
	 */
	private function registraLog() {
		//Define o arquivo que será criado ou gravado caso exista
		$log = APP_DIR."log".DS."error_log_" . date("Ymd") . ".txt";
	
		//Se não existe arquivo, cria
		if( !file_exists($log)){
	
			$msg = "/************************************************** " . "\r\n".
					" * Arquivo de log " ."\r\n".
					" * " ."\r\n".
					" * Data Criação: " . date("d/m/Y H:i") . " " ."\r\n".
					" *************************************************/ " ."\r\n".
					"——————————————————————- "."\r\n";
	
		}else{
			$msg = "";
		}
		//Abre o arquivo
		if($fp = fopen( $log , "a+")) {
			$trace = $this->exception->getTraceAsString();
			$trace = explode("#", $trace);
			$trace = implode("\r\n", $trace);
			$usuario = (isset($_SESSION['LGF']['identidade'])) ? $_SESSION['LGF']['identidade']  : "não está logado";
			//Arquivo
			$msg .= "Classe: ".__CLASS__." Usuario ID: $usuario"." Request: ".$_SERVER['REQUEST_URI']."\r\n";
			$msg .= "Hora: ".date("d/m/Y - H:i:s") . " " ."\r\n";
			$msg .= "Erro [ {$this->exception->getCode()} ]: ".$this->exception->getFile()." ( Linha: {$this->exception->getLine()} ) " ."\r\n";
			$msg .= "Mensagem Usuário:{$this->getMensagem($this->exception->getCode())} " ."\r\n";
			$msg .= "Mensagem Interna:{$this->exception->getMessage()} " ."\r\n";
			$msg .= "Trace: "."\r\n";
			$msg .= "{".$trace."} " ."\r\n";
			$msg .= "——————————————————————- "."\r\n"."\r\n";
	
					//Grava
                fwrite($fp, $msg);
			fclose($fp);
		}
	
	}
}
?>