<?php
namespace exceptions;
class ExceptionHandler{
	
	private $exception;
	private $codigoErro;
	private $trace;
	
	private function __construct(\Exception $exception, $codigoErro = false){
		$this->exception = $exception;
		$this->codigoErro = ($codigoErro) ? $codigoErro : $exception->getCode();
		if(DEBUG_LIGADO){ $this->debug(); }
	}
	
	public static function tratarErro(\Exception $e,$codigo = false){
		if($codigo === false){
			$codigo = $e->getCode();
		}
		$_ExHandler = new ExceptionHandler($e,$codigo);
		$_ExHandler->registraLog();
		return $_ExHandler->getMensagem()."<p><div class='text-left'>".$_ExHandler->trace."</div></p>";
	}
	
	private function debug(){
		$this->trace = "<pre>";
		$this->trace.= $this->exception->getMessage()."<br>";
		$this->trace.= print_r($this->exception->getTraceAsString(),true)."<br>";
		$this->trace.= $this->exception->getFile()."<br>";
		$this->trace.= "Codigo: ".$this->exception->getCode();
		$this->trace.= "</pre>";
	}
	
	private function getMensagem(){
		switch ($this->codigoErro){
			case 0 : return "Ocorreu um problema na execução do Script.";
			case 1 : return "Erro de SQL";
			case 1002 : return "Ocorreu um erro no site. Entre em contato com o suporte.";
			case 404 : return "Página Não Encontrada";
			case 23000:	return "Não foi possível gravar os dados. Tente novamente.";
			default: return "Erro Não Identificado, código: ".$this->codigoErro;
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
			$usuario = ($_SESSION['logado']) ? $_SESSION['LGF']['identidade'] : "não está logado";
			//Arquivo
			$msg .= "Classe: ".__CLASS__." Usuario ID: $usuario"." Request: ".$_SERVER['REQUEST_URI']."\r\n".
					"Hora: ".date("d/m/Y - H:i:s") . " " ."\r\n".
					"Erro [ {$this->exception->getCode()} ]: ".$this->exception->getFile()." linha ( {$this->exception->getLine()} ) " ."\r\n".
					"Mensagem Usuário:{$this->getMensagem()} " ."\r\n".
					"Mensagem Interna:{$this->exception->getMessage()} " ."\r\n".
					"Trace: "."\r\n".
					"{".$trace."} " ."\r\n".
					"——————————————————————- "."\r\n"."\r\n";
	
					//Grava
                fwrite($fp, $msg);
			fclose($fp);
		}
	
	}
}
?>