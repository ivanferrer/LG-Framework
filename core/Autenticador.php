<?php 
namespace core;
use exceptions as e;
abstract class Autenticador{
	private $ip;
	private $time;
	private $logado;
	private $sessionId;
	private $_classLogin;
	private $mensagemLoginInvalido;
	protected $dadosSession;
	protected $identidade;
	protected $dao;
	
	public function __construct(Modelo $classeIdentidade){
		$this->mensagemLoginInvalido = 'Área Restrita';
		$this->identidade = $classeIdentidade;
		$callDao = str_replace("model","dao",get_class($this->identidade));
		$this->dao = new $callDao($this->identidade);
		unset($callDao);
		//$this->_classLogin = CONTROLLER_LOGIN;
		$this->ip = getenv("REMOTE_ADDR");
		$this->time = date("Y-m-d H:i:s");
		$this->sessionId = session_id();
		if(isset($_SESSION['logado'])){ $this->logado = true; }else{ $this->logado = false; };
		//$this->renovarSessao();
	}

	abstract protected function verificarDados();
	abstract protected function setDadosSessao();

	private function renovarSessao(){
		session_regenerate_id(true);
	}

	private static function destruirSessao(){
		session_unset();
		session_destroy();
		header("Location: ".HTTP_PATH);
	}
	
	public function verificarPermissao($obj){
		$metodosAutenticados = $obj->getMetodosAutenticados();
		$metodosNaoAutenticados = $obj->getMetodosNaoAutenticados();
		if($this->logado === false && !($_SESSION['classe'] == CONTROLLER_LOGIN && $_SESSION['metodo'] == METODO_LOGIN)){
			$_SESSION['url'] = HTTP_FULL_PATH;
			if(
					(	$metodosAutenticados == 'todos'	||	array_search($_SESSION['metodo'],$metodosAutenticados) !== false	)
					&&
					(	$metodosNaoAutenticados != 'todos'	&&	array_search($_SESSION['metodo'],$metodosNaoAutenticados) === false	)
			){
				throw new e\PermissionException($this->mensagemLoginInvalido);
			}
		}
	}

	// TODO: revisar maneira como framework monta o menu
	// TODO: revisar maneira como framework verifica se o perfil tem acesso à página requisitada
	public function verificarPermissaoAdicional(){
		//
	}

	public function logout(){
		$this->destruirSessao();
	}
	
	public function autenticar(){
		try{
			$this->verificarDados();
			$this->setDadosSessao();
			$_SESSION['logado'] = true;
			if(isset($_SESSION['url'])){
				header("Location: ".$_SESSION['url']);
			}else{
				header("Location: ".HTTP_FULL_PATH);
			}
		}catch(e\PermissionException $e){
			throw $e;
		}
	}
	
	public function setMesagemLoginInvalido($mensagem){
		$this->mensagemLoginInvalido = $mensagem;
	}
	
	public function getLoginForm($labelLogin='Login',$labelSenha = 'Senha',$botao = 'Entrar'){
		return "
		<div id='div-login' class='login'>
			<form id='form-login' class='login' name='login' action='".HTTP_PATH.$this->_classLogin."/autenticar' method='POST'>
				<span id='span-login-login'><label>$labelLogin</label><input class='text' type='text' name='login'/></span>
				<span id='span-login-password'><label>$labelSenha</label><input class='text' type='password' name='senha'/></span>
				<span id='span-login-submit'><input class='botao' type='submit' value='$botao'/></span>
			</form>
		</div>";
	}
	
}
?>