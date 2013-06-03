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
		if(isset($_SESSION['LGF']['logado']) && $_SESSION['LGF']['logado'] == hash("sha256",APP_DIR.PROJETO_NOME)){
		    $this->logado = true;
		}else{
		    $this->logado = false;
		    $_SESSION['LGF']['logado'] = false;
		};
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
		header("Location: ".HTTP_PATH.LGF_SUBAPP);
	}
	
	public function verificarPermissao($obj){
		if(Globals::getTipo() == 'controller'){
			$metodosAcessiveisPorUrl = $obj->getMetodosAcessiveisPorUrl();
			$metodosNaoAcessiveisPorUrl = $obj->getMetodosNaoAcessiveisPorUrl();
			if(
					(	$metodosNaoAcessiveisPorUrl == 'todos'	||	array_search($_SESSION['metodo'],$metodosNaoAcessiveisPorUrl) !== false	)
					&&
					(	$metodosAcessiveisPorUrl != 'todos'	&&	array_search($_SESSION['metodo'],$metodosAcessiveisPorUrl) === false	)
			){
				throw new e\PageException(null,404);
			}
		}
		$metodosAutenticados = $obj->getMetodosAutenticados();
		$metodosNaoAutenticados = $obj->getMetodosNaoAutenticados();
		if(
				(	$metodosAutenticados == 'todos'	||	array_search($_SESSION['metodo'],$metodosAutenticados) !== false	)
				&&
				(	$metodosNaoAutenticados != 'todos'	&&	array_search($_SESSION['metodo'],$metodosNaoAutenticados) === false	)
		){
    		if($this->logado === false && !($_SESSION['classe'] == CONTROLLER_LOGIN && $_SESSION['metodo'] == METODO_LOGIN)){
    			    $_SESSION['LGF']['url'] = HTTP_FULL_PATH.$_SESSION['classe'].'/'.$_SESSION['metodo'];
    				throw new e\PermissionException($this->mensagemLoginInvalido);
    		}else{
    		    if(method_exists($this, 'permissaoDeAcesso')){
    		        $resposta = $this->permissaoDeAcesso($this->identidade,$_SESSION['classe'],$_SESSION['metodo']);
    		        if(!$resposta){
        				throw new e\PermissionException("O seu perfil não tem acesso a esta página");
    		        }
    		    }
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
			$_SESSION['LGF']['logado'] = hash("sha256",APP_DIR.PROJETO_NOME);
			//$getter = "get".str_replace(" ","",ucwords(str_replace("_"," ",$this->identidade->getPrimaryKey())));
			$_SESSION['LGF']['identidade'] = $this->identidade->getAutoIncrementId();
			if(isset($_SESSION['LGF']['url']) && strstr(HTTP_FULL_PATH, $_SESSION['LGF']['url'])){
				header("Location: ".$_SESSION['LGF']['url']);
			}else{
				header("Location: ".HTTP_FULL_PATH.LGF_SUBAPP);
			}
		}catch(e\PermissionException $e){
			throw $e;
		}
	}
	
	public function setMesagemLoginInvalido($mensagem){
		$this->mensagemLoginInvalido = $mensagem;
	}
	
}
?>