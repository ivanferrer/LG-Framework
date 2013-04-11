<?php
namespace core;
use \exceptions as e;
class Roteador{
	
	private $url;
	private $tipo;
	private $classe;
	private $metodo;
	private $obj;
	private $parametros;
	private $alert;
	private $urlFinal;
	
	public function __construct($sysAction){
		$actions = explode("/", $sysAction);
		$this->urlFinal = "";
		if($actions[0] == 'setup' && HABILITA_SETUP == true){
			throw new e\SetupException();
		}
		if($actions[0] == 'c'){
			$this->urlFinal .= "c/";
			unset($actions[0]);
			if(count($actions) > 0){
				$actions = array_values($actions);
				if($actions[0] == ''){
					throw new e\PageException(null,404);
				}else{
					$this->tipo = 'controller';
				}
			}else{
				throw new e\PageException(null,404);
			}
		}else{
			$this->tipo = 'view';
		}
		$this->classe = ($actions[0] != '') ? $actions[0] : VIEW_PADRAO;
		$this->urlFinal .= $this->classe;
		$this->classe = $this->transformarParametrosURL($this->classe,true);
		$this->metodo = (isset($actions[1]) && $actions[1] != '') ? $actions[1] : METODO_PADRAO;
		$this->urlFinal .= "/".$this->metodo;
		$this->metodo = $this->transformarParametrosURL($this->metodo);
		if(count($actions)>2){
			for($i=0;$i<count($actions)-2;$i++){
				$this->parametros[] = $actions[$i+2];
			}
		}
	}
	
	public function instanciarObj(){
		if(file_exists(APP_DIR.$this->tipo.DS."$this->classe.php")){
			$x = "call";
			$$x = $this->tipo."\\".$this->classe;
			$this->obj = new $call();
		}else{
			throw new e\PageException(null,404);
		}
		
	}
	
	public function registrarSession(){
    		if(isset($_SESSION['LGF_alerta'])){
    			$this->obj->setAlerta($_SESSION['LGF_alerta']['mensagem'],$_SESSION['LGF_alerta']['classe']);
    			unset($_SESSION['LGF_alerta']);
    		}
    		if(isset($_SESSION['anterior'])){
    		    if($_SESSION['tipo'] == 'view'){
        		    $_SESSION['anterior']['tipo'] = $_SESSION['tipo'];
        			$_SESSION['anterior']['metodo'] = $_SESSION['metodo'];
        			$_SESSION['anterior']['classe'] = $_SESSION['classe'];
        			$_SESSION['anterior']['urlChamada'] = $_SESSION['urlChamada'];
    		    }
    		}else{
    		    $_SESSION['anterior']['tipo'] = null;
    			$_SESSION['anterior']['metodo'] = null;
    			$_SESSION['anterior']['classe'] = null;
    			$_SESSION['anterior']['urlChamada'] = null;
    		}
    		$_SESSION['tipo'] = $this->tipo;
    		$_SESSION['metodo'] = $this->metodo;
    		$_SESSION['classe'] = $this->classe;
    		$_SESSION['urlChamada'] = $this->urlFinal;
    		
    		Globals::setTipo($_SESSION['tipo']);
    		Globals::setMetodo($_SESSION['metodo']);
    		Globals::setClasse($_SESSION['classe']);
    		Globals::setUrlChamada($_SESSION['urlChamada']);
    		Globals::setAnterior($_SESSION['anterior']);
		
	}
	
	public function verificarPermissao(){
		try{
			if(!($this->obj instanceof Autenticador)){
				if($this->classe != VIEW_LOGIN){
					$_classLogin = CONTROLLER_LOGIN;
					$login = new $_classLogin();
					$login->verificarPermissao($this->obj);
					unset($_classLogin);
					unset($login);
				}else{
					if($this->metodo != METODO_LOGIN){
						$_classLogin = CONTROLLER_LOGIN;
						$login = new $_classLogin();
						$login->verificarPermissao($this->obj);
						unset($_classLogin);
						unset($login);
					}
				}
			}
		}catch(e\PermissionException $e){
			throw $e;
		}
	}
	
	public function chamarMetodo(){
		if(method_exists($this->obj, $this->metodo)){
			$reflection = new \ReflectionMethod($this->obj, $this->metodo);
			if($reflection->isPublic()){
				if(isset($this->parametros)){
					$reflection->invokeArgs($this->obj, $this->parametros);
				}else{
					$call = $this->metodo;
					$this->obj->$call();
				}
			}else{
				throw new \Exception("Esta página não pode ser acessada.");
			}
		}else{
			throw new e\PageException(null,404);
		}
	}
	
	private function transformarParametrosURL($parametro,$classe=false){
		$base = ($classe) ? 0 : 1;
		$parametro = explode('-', $parametro);
		$return = '';
		if(!$classe){
			$return = $parametro[0];
		}
		if(count($parametro)>0){
			for($i = $base;$i<count($parametro);$i++){
				$return .= ucfirst($parametro[$i]);
			}
		}
		return $return;
	}
	
	public function exibir(){
		if($this->tipo != 'controller'){
			$this->obj->exibir();
		}
	}
	
}

?>