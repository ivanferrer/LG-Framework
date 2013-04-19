<?php
namespace core;
abstract class Pagina {
	private $metodosAutenticados;
	private $metodosNaoAutenticados;
	protected $autenticacao;
	protected $obj;
	protected $dao;
	protected $list;
	protected $smarty;
	protected $generalContent;
	protected $template;
			
	public function __construct() {
		$this->metodosAutenticados = array();
		$this->metodosNaoAutenticados = array();
		$_classLogin = CONTROLLER_LOGIN;
		$this->autenticacao = new $_classLogin();
		unset($_classLogin);
		$this->smarty = new Smarty();
		$this->associarDados('LGF_alerta',$_SESSION['LGF_alerta']);
		$this->generalContent = '';
		$this->template = TEMPLATE_PADRAO;
	}

	protected function setMetodosAutenticados($valor) {
		$this->metodosAutenticados = $valor;
	}
	protected function setMetodosNaoAutenticados($valor) {
		$this->metodosNaoAutenticados = $valor;
	}

	public function getMetodosAutenticados() {
		return $this->metodosAutenticados;
	}
	public function getMetodosNaoAutenticados() {
		return $this->metodosNaoAutenticados;
	}

	public function addGeneralContent($value) {
		$this->generalContent = $value;
	}

	public function getGeneralContent() {
		return $this->generalContent;
	}

	public function setTemplate($value) {
		if (file_exists("template".DS.$value)) {
			$this->template = $value;
		} else {
			throw new \Exception("O Template $value não existe", 1002);
		}
	}

	public function associarDados($nomeVariavel, $parametro) {
		$this->smarty->assign($nomeVariavel, $parametro);
	}	

	public function exibir() {
		$this->associarDados("generalContent", $this->getGeneralContent());
		$this->smarty->display($this->template);
	}
	

}
