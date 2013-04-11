<?php
namespace exceptions;
class ExceptionHandler{
	
	private $exception;
	private $codigoErro;
	
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
		return $_ExHandler->getMensagem();
	}
	
	private function debug(){
		echo "<pre>";
		echo $this->exception->getMessage()."<br>";
		print_r($this->exception->getTraceAsString())."<br>";
		echo $this->exception->getFile()."<br>";
		echo "Mensagem Usuário: ".$this->getMensagem()."<br>";
		echo "Codigo: ".$this->exception->getCode();
		echo "</pre>";
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
			//Arquivo
			$msg .= "Classe: ".__CLASS__." Usuario: "."\r\n".//.$_SESSION['usr_id']."\r\n".
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